<?php

namespace App\Services;
use App\Models\Appointment;
use App\Models\Queue;
use App\Models\Prescription;
use App\Models\DoctorLeave;
use App\Models\DoctorSchedule;
use App\Models\MedicalEncounter;
use App\Models\TelehealthSession;
use App\Mail\GeneralNotificationMail;
use App\Models\Notification;
use App\Models\UserNotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NotificationService
{
    /**
     * Create a notification.
     *
     * @throws ValidationException
     */
    public function createNotification(array $data): Notification
    {
        try {

            return DB::transaction(function () use ($data) {

                $user = User::findOrFail($data['user_id']);

                // Respect user notification preferences
                $channel = $data['channel'] ?? 'general';
                if (($data['type'] ?? '') === 'email' && !$this->userWantsEmail($user->id, $channel)) {
                    // Downgrade email to in_app if user opted out
                    $data['type'] = 'in_app';
                }

                $notification = Notification::create([

                    'user_id' => $user->id,

                    'type' => $data['type'],

                    'channel' => $data['channel'],

                    'reference_id' => $data['reference_id'] ?? null,

                    'subject' => $data['subject'] ?? null,

                    'content' => $data['content'],

                    'status' => 'pending',

                    'retry_count' => 0,

                ]);

                if ($notification->type === 'email') {

                    $this->sendEmailNotification($notification);

                } else {

                    $notification->update([

                        'status' => 'sent',

                        'sent_at' => now(),

                    ]);

                }

                return $this->loadRelations($notification);
            });

        } catch (ModelNotFoundException $e) {

            throw ValidationException::withMessages([
                'user' => [
                    'User not found.'
                ]
            ]);

        }
    }

    /**
     * Send notification email.
     */
    public function sendEmailNotification(
        Notification $notification
    ): Notification {

        try {

            Mail::to(
                $notification->user->email
            )->send(

                new GeneralNotificationMail(
                    $notification
                )

            );

            $notification->update([

                'status' => 'sent',

                'sent_at' => now(),

                'error_message' => null,

            ]);

        } catch (\Throwable $e) {

            $notification->update([

                'status' => 'failed',

                'error_message' => $e->getMessage(),

                'retry_count' => $notification->retry_count + 1,

            ]);

        }

        return $notification->fresh();
    }

    /**
     * Load notification relationships.
     */
    private function loadRelations(
        Notification $notification
    ): Notification {

        return $notification->load([
            'user',
        ]);
    }

    /**
     * Find notification or fail.
     *
     * @throws ValidationException
     */
    private function findNotificationOrFail(
        string $notificationId
    ): Notification {

        try {

            return Notification::findOrFail(
                $notificationId
            );

        } catch (ModelNotFoundException $e) {

            throw ValidationException::withMessages([
                'notification' => [
                    'Notification not found.'
                ]
            ]);

        }
    }
    /**
 * Get all notifications for a user.
 */
public function getUserNotifications(
    string $userId,
    bool $unreadOnly = false
) {
    $query = Notification::with('user')
        ->where('user_id', $userId)
        ->latest();

    if ($unreadOnly) {
        $query->where('status', '!=', 'read');
    }

    return $query->get();
}

/**
 * Mark a notification as read.
 *
 * @throws ValidationException
 */
public function markAsRead(
    string $notificationId
): Notification {

    return DB::transaction(function () use ($notificationId) {

        $notification = $this->findNotificationOrFail(
            $notificationId
        );

        if ($notification->status === 'read') {
            return $this->loadRelations($notification);
        }

        $notification->update([

            'status' => 'read',

        ]);

        return $this->loadRelations(
            $notification->fresh()
        );

    });
}

/**
 * Delete a notification.
 *
 * @throws ValidationException
 */
public function deleteNotification(
    string $notificationId
): bool {

    return DB::transaction(function () use ($notificationId) {

        $notification = $this->findNotificationOrFail(
            $notificationId
        );

        return (bool) $notification->delete();

    });
}

/**
 * Retry sending a failed notification.
 *
 * @throws ValidationException
 */
public function retryFailedNotification(
    string $notificationId
): Notification {

    return DB::transaction(function () use ($notificationId) {

        $notification = $this->findNotificationOrFail(
            $notificationId
        );

        if ($notification->status !== 'failed') {

            throw ValidationException::withMessages([
                'notification' => [
                    'Only failed notifications can be retried.'
                ]
            ]);

        }

        $notification->update([

            'status' => 'pending',

        ]);

        if ($notification->type === 'email') {

            return $this->sendEmailNotification(
                $notification
            );

        }

        $notification->update([

            'status' => 'sent',

            'sent_at' => now(),

        ]);

        return $this->loadRelations(
            $notification->fresh()
        );

    });
}
/**
 * Send an appointment notification.
 */
public function sendAppointmentNotification(
    User $user,
    string $subject,
    string $content,
    bool $sendEmail = true
): Notification {

    return $this->createNotification([
        'user_id' => $user->id,
        'type'    => $sendEmail ? 'email' : 'in_app',
        'channel' => 'appointment',
        'subject' => $subject,
        'content' => $content,
    ]);
}

/**
 * Send appointment reminder.
 */
public function sendAppointmentReminder(
    Appointment $appointment
): Notification {

    $patient = $appointment->patient->user;

    $doctor = $appointment->doctor->user;

    $hospital = $appointment->hospital;

    $subject = 'Appointment Reminder';

    $content =
        "Reminder: You have an appointment tomorrow with Dr. {$doctor->first_name} {$doctor->last_name} ".
        "at {$hospital->name}. Please arrive 15 minutes early.";

    return $this->createNotification([
        'user_id'      => $patient->id,
        'type'         => 'email',
        'channel'      => 'appointment',
        'reference_id' => (string) $appointment->id,
        'subject'      => $subject,
        'content'      => $content,
    ]);
}

/**
 * Send queue notification.
 * Notifies the patient (if appointment-based) and the doctor.
 * Walk-in patients have no appointment/patient record — patient notify is skipped.
 */
public function sendQueueNotification(Queue $queue): ?Notification
{
    $patientContent = match ($queue->status) {
        'waiting'         => 'You have been added to the waiting queue.',
        'called'          => 'Your queue number has been called. Please proceed to the consultation room.',
        'in_consultation' => 'Your consultation has started.',
        'completed'       => 'Your consultation has been completed. Thank you for visiting.',
        default           => 'Your queue status has changed.',
    };

    $doctorContent = match ($queue->status) {
        'waiting'         => 'A new patient has joined your queue.',
        'in_consultation' => 'Queue #' . $queue->queue_number . ' — patient is now in consultation.',
        'completed'       => 'Queue #' . $queue->queue_number . ' — consultation completed.',
        default           => 'Queue status updated.',
    };

    $result = null;

    // Notify patient (appointment-based only)
    if ($queue->appointment_id && $queue->appointment?->patient) {
        try {
            $result = $this->createNotification([
                'user_id'      => $queue->appointment->patient->user->id,
                'type'         => 'in_app',
                'channel'      => 'queue',
                'reference_id' => (string) $queue->id,
                'subject'      => 'Queue Update',
                'content'      => $patientContent,
            ]);
        } catch (\Throwable) { /* silent */ }
    }

    // Notify doctor about queue changes
    try {
        $doctorUser = User::find($queue->doctor_id);
        if ($doctorUser && in_array($queue->status, ['waiting', 'in_consultation', 'completed'])) {
            $this->createNotification([
                'user_id'      => $doctorUser->id,
                'type'         => 'in_app',
                'channel'      => 'queue',
                'reference_id' => (string) $queue->id,
                'subject'      => 'Queue Update',
                'content'      => $doctorContent,
            ]);
        }
    } catch (\Throwable) { /* silent */ }

    return $result;
}

/**
 * Send prescription notification.
 * Notifies the patient AND the doctor.
 */
public function sendPrescriptionNotification(
    Prescription $prescription
): Notification {

    $patient = $prescription
        ->encounter
        ->patient
        ->user;

    $subject = 'Prescription Update';

    $patientContent = match ($prescription->status) {
        'active'    => 'A new prescription has been created for you.',
        'completed' => 'Your prescription has been completed.',
        'cancelled' => 'Your prescription has been cancelled.',
        default     => 'Your prescription has been updated.',
    };

    $doctorContent = match ($prescription->status) {
        'active'    => 'Prescription successfully created for patient.',
        'completed' => 'A prescription has been marked as completed.',
        'cancelled' => 'A prescription has been cancelled.',
        default     => 'A prescription has been updated.',
    };

    $result = $this->createNotification([
        'user_id'      => $patient->id,
        'type'         => 'in_app',
        'channel'      => 'prescription',
        'reference_id' => (string) $prescription->id,
        'subject'      => $subject,
        'content'      => $patientContent,
    ]);

    // Also notify the doctor
    try {
        $doctorUser = $prescription->encounter->doctor->user ?? null;
        if ($doctorUser) {
            $this->createNotification([
                'user_id'      => $doctorUser->id,
                'type'         => 'in_app',
                'channel'      => 'prescription',
                'reference_id' => (string) $prescription->id,
                'subject'      => $subject,
                'content'      => $doctorContent,
            ]);
        }
    } catch (\Throwable) { /* silent */ }

    return $result;
}

/**
 * Send telehealth notification.
 */
public function sendTelehealthNotification(
    TelehealthSession $session,
    ?string $subject = null,
    ?string $content = null,
    bool $sendEmail = true
): Notification {

    $patient = $session->appointment->patient;
    $user    = $patient->user;

    if (!$subject) {
        $subject = 'Telehealth Session';
    }

    if (!$content) {
        $content = match ($session->status) {
            'scheduled' =>
                "Your telehealth session has been scheduled.\n\nMeeting Link:\n{$session->session_url}",
            'active' =>
                "Your telehealth session has started.\n\nJoin Here:\n{$session->session_url}",
            'completed' =>
                'Your telehealth consultation has been completed.',
            'cancelled' =>
                'Your telehealth consultation has been cancelled.',
            default =>
                'Your telehealth session has been updated.',
        };
    }

    return $this->createNotification([
        'user_id'      => $user->id,
        'type'         => $sendEmail ? 'email' : 'in_app',
        'channel'      => 'telehealth',
        'reference_id' => (string) $session->id,
        'subject'      => $subject,
        'content'      => $content,
    ]);
}
/**
 * Send doctor leave notification.
 * On submit: notify only the hospital_admin(s) of the doctor's hospital.
 * On approved/rejected: notify the doctor.
 */
public function sendDoctorLeaveNotification(
    DoctorLeave $leave,
    string $action
): void {

    if ($action === 'submitted') {

        // Resolve the doctor's hospital_id from HealthcareProvider
        $doctorHospitalId = $leave->doctor?->hospital_id ?? null;

        // Find hospital admins scoped to the doctor's hospital (active staff records)
        $admins = collect();
        if ($doctorHospitalId) {
            $admins = User::whereHas('roles', fn ($q) => $q->where('name', 'hospital_admin'))
                ->whereHas('hospitalStaff', fn ($hs) =>
                    $hs->where('hospital_id', $doctorHospitalId)
                       ->where('is_active', true)
                )
                ->get();
        }

        // Fallback: if no scoped admins found, notify all hospital admins
        if ($admins->isEmpty()) {
            $admins = User::whereHas('roles', fn ($q) => $q->where('name', 'hospital_admin'))->get();
        }

        $doctorName = trim(
            ($leave->doctor?->user?->first_name ?? '') . ' ' .
            ($leave->doctor?->user?->last_name ?? '')
        ) ?: 'A doctor';

        $leaveDate = $leave->leave_date instanceof \Carbon\Carbon
            ? $leave->leave_date->toDateString()
            : (string) $leave->leave_date;

        foreach ($admins as $admin) {
            try {
                $this->createNotification([
                    'user_id'      => $admin->id,
                    'type'         => 'in_app',
                    'channel'      => 'doctor_leave',
                    'reference_id' => (string) $leave->id,
                    'subject'      => 'New Leave Request',
                    'content'      => "{$doctorName} submitted a leave request for {$leaveDate}.",
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Failed to send leave notification to admin ' . $admin->id . ': ' . $e->getMessage());
            }
        }

        return;
    }

    $message = $action === 'approved'
        ? 'Your leave request has been approved.'
        : 'Your leave request has been rejected.';

    try {
        $this->createNotification([
            'user_id'      => $leave->doctor->user->id,
            'type'         => 'in_app',
            'channel'      => 'doctor_leave',
            'reference_id' => (string) $leave->id,
            'subject'      => 'Leave Request Update',
            'content'      => $message,
        ]);
    } catch (\Throwable) { /* silent */ }
}
public function sendDoctorScheduleNotification(
    DoctorSchedule $schedule,
    string $action
): void {

    $messages = [
        'created' => ['subject' => 'Doctor Schedule Created', 'content' => 'A new work schedule has been assigned to you.'],
        'updated' => ['subject' => 'Doctor Schedule Updated', 'content' => 'Your work schedule has been updated.'],
        'deleted' => ['subject' => 'Doctor Schedule Removed', 'content' => 'Your work schedule has been removed.'],
    ];

    $message = $messages[$action] ?? ['subject' => 'Schedule Update', 'content' => 'Your schedule has been updated.'];

    try {
        $this->createNotification([
            'user_id'      => $schedule->doctor->user->id,
            'type'         => 'in_app',
            'channel'      => 'doctor_schedule',
            'reference_id' => (string) $schedule->id,
            'subject'      => $message['subject'],
            'content'      => $message['content'],
        ]);
    } catch (\Throwable) { /* silent */ }
}
/**
 * Send medical encounter notification.
 * Notifies both the patient AND the doctor.
 */
public function sendMedicalEncounterNotification(
    MedicalEncounter $encounter,
    string $action
): void {
    $patientMessages = [
        'started'   => ['subject' => 'Consultation Started',   'content' => 'Your medical consultation has started.'],
        'completed' => ['subject' => 'Consultation Completed', 'content' => 'Your consultation has been completed. Your prescription and medical record are now available.'],
    ];

    $doctorMessages = [
        'started'   => ['subject' => 'Consultation Started',   'content' => 'Consultation started for patient ' . ($encounter->patient->user->first_name ?? '') . ' ' . ($encounter->patient->user->last_name ?? '') . '.'],
        'completed' => ['subject' => 'Consultation Completed', 'content' => 'Consultation completed for patient ' . ($encounter->patient->user->first_name ?? '') . ' ' . ($encounter->patient->user->last_name ?? '') . '.'],
    ];

    $patientMsg = $patientMessages[$action] ?? null;
    $doctorMsg  = $doctorMessages[$action] ?? null;
    if (!$patientMsg) return;

    // Notify patient
    try {
        $this->createNotification([
            'user_id'      => $encounter->patient->user->id,
            'type'         => 'in_app',
            'channel'      => 'medical_encounter',
            'reference_id' => (string) $encounter->id,
            'subject'      => $patientMsg['subject'],
            'content'      => $patientMsg['content'],
        ]);
    } catch (\Throwable) { /* silent */ }

    // Notify doctor
    try {
        $doctorUser = $encounter->doctor->user ?? null;
        if ($doctorUser && $doctorMsg) {
            $this->createNotification([
                'user_id'      => $doctorUser->id,
                'type'         => 'in_app',
                'channel'      => 'medical_encounter',
                'reference_id' => (string) $encounter->id,
                'subject'      => $doctorMsg['subject'],
                'content'      => $doctorMsg['content'],
            ]);
        }
    } catch (\Throwable) { /* silent */ }
}

/**
 * Send notification when admin reassigns a patient to a different doctor.
 */
public function sendAdminRescheduleNotification(
    Appointment $appointment,
    string $oldDoctorName,
    string $newDoctorName
): void {
    try {
        $user = $appointment->patient->user;
        $this->createNotification([
            'user_id'      => $user->id,
            'type'         => 'email',
            'channel'      => 'appointment',
            'reference_id' => (string) $appointment->id,
            'subject'      => 'Appointment Reassigned',
            'content'      => "Your appointment has been reassigned from Dr. {$oldDoctorName} to Dr. {$newDoctorName} due to a schedule change. The date and time remain the same.",
        ]);
    } catch (\Throwable) { /* silent */ }
}

/**
 * Notify the receptionist(s) and hospital admin(s) of a hospital about a new appointment.
 */
public function sendStaffAppointmentNotification(
    Appointment $appointment,
    string $subject,
    string $content
): void {
    $hospitalId = $appointment->hospital_id;

    // Notify receptionists of the same hospital
    $receptionists = User::whereHas('roles', fn ($q) => $q->where('name', 'receptionist'))
        ->whereHas('hospitalStaff', fn ($q) => $q->where('hospital_id', $hospitalId))
        ->get();

    foreach ($receptionists as $user) {
        try {
            $this->createNotification([
                'user_id'      => $user->id,
                'type'         => 'in_app',
                'channel'      => 'appointment',
                'reference_id' => (string) $appointment->id,
                'subject'      => $subject,
                'content'      => $content,
            ]);
        } catch (\Throwable) { /* silent */ }
    }

    // Notify hospital admins of the same hospital
    $admins = User::whereHas('roles', fn ($q) => $q->where('name', 'hospital_admin'))
        ->whereHas('hospitalStaff', fn ($q) => $q->where('hospital_id', $hospitalId))
        ->get();

    foreach ($admins as $user) {
        try {
            $this->createNotification([
                'user_id'      => $user->id,
                'type'         => 'in_app',
                'channel'      => 'appointment',
                'reference_id' => (string) $appointment->id,
                'subject'      => $subject,
                'content'      => $content,
            ]);
        } catch (\Throwable) { /* silent */ }
    }
}

/**
 * Get user notification preferences (create defaults if none exist).
 */
public function getOrCreatePreferences(string $userId): UserNotificationPreference
{
    return UserNotificationPreference::firstOrCreate(
        ['user_id' => $userId],
        [
            'email_enabled'         => true,
            'sms_enabled'           => true,
            'push_enabled'          => true,
            'appointment_reminders' => true,
            'queue_updates'         => true,
            'promotional'           => false,
        ]
    );
}

/**
 * Check if user wants email notifications for a given channel.
 */
public function userWantsEmail(string $userId, string $channel = 'general'): bool
{
    $prefs = UserNotificationPreference::where('user_id', $userId)->first();
    if (!$prefs) return true; // default: send

    if (!$prefs->email_enabled) return false;

    return match ($channel) {
        'appointment' => $prefs->appointment_reminders,
        'queue'       => $prefs->queue_updates,
        'promotional' => $prefs->promotional,
        default       => true,
    };
}
}