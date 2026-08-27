<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentReferral;
use App\Models\HealthcareProvider;
use App\Models\Queue;
use App\Services\AppointmentToQueueService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentReferralController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private AppointmentToQueueService $queueService,
    ) {}

    /**
     * Doctor refers an appointment to another doctor / department.
     * POST /api/appointments/{appointment}/refer
     */
    public function refer(Request $request, Appointment $appointment)
    {
        $user = auth()->user();

        // Only the assigned doctor can refer
        if (! $user->hasRole('doctor') || $appointment->doctor_id !== $user->id) {
            abort(403, 'Only the assigned doctor can refer this appointment.');
        }

        if (! in_array($appointment->status, ['pending', 'confirmed'])) {
            throw ValidationException::withMessages([
                'appointment' => ['Only pending or confirmed appointments can be referred.'],
            ]);
        }

        $data = $request->validate([
            'referred_to_doctor_id'     => ['nullable', 'uuid', 'exists:healthcare_providers,id'],
            'referred_to_department_id' => ['nullable', 'uuid', 'exists:departments,id'],
            'reason'                    => ['required', 'string', 'max:1000'],
        ]);

        if (empty($data['referred_to_doctor_id']) && empty($data['referred_to_department_id'])) {
            throw ValidationException::withMessages([
                'referred_to_doctor_id' => ['You must specify a doctor or department to refer to.'],
            ]);
        }

        return DB::transaction(function () use ($appointment, $data, $user) {

            $referral = AppointmentReferral::create([
                'appointment_id'            => $appointment->id,
                'referred_by'               => $user->id,
                'referred_to_doctor_id'     => $data['referred_to_doctor_id'] ?? null,
                'referred_to_department_id' => $data['referred_to_department_id'] ?? null,
                'reason'                    => $data['reason'],
                'status'                    => 'pending',
            ]);

            // If a specific doctor was chosen, reassign the appointment to them
            if (! empty($data['referred_to_doctor_id'])) {
                $appointment->update([
                    'doctor_id' => $data['referred_to_doctor_id'],
                    'status'    => 'pending',   // reset so new doctor must confirm
                ]);

                // Remove any existing queue entry for this appointment so the
                // patient no longer appears in Doctor A's queue.
                Queue::where('appointment_id', $appointment->id)->delete();

                // Notify the new doctor
                $newDoctor = HealthcareProvider::with('user')->find($data['referred_to_doctor_id']);
                if ($newDoctor?->user) {
                    $this->notificationService->createNotification([
                        'user_id'      => $newDoctor->user->id,
                        'type'         => 'in_app',
                        'channel'      => 'appointment',
                        'reference_id' => (string) $appointment->id,
                        'subject'      => 'Patient Referred to You',
                        'content'      => "Dr. {$user->first_name} {$user->last_name} has referred a patient to you. Reason: {$data['reason']}",
                    ]);
                }
            }

            // Notify the patient
            $referringDoctorName = trim("{$user->first_name} {$user->last_name}");
            $toWhere = '';
            if (! empty($data['referred_to_doctor_id'])) {
                $newDoc = HealthcareProvider::with('user')->find($data['referred_to_doctor_id']);
                if ($newDoc?->user) {
                    $toWhere = "to Dr. {$newDoc->user->first_name} {$newDoc->user->last_name}";
                }
            }
            if (! empty($data['referred_to_department_id'])) {
                $dept = \App\Models\Department::find($data['referred_to_department_id']);
                $toWhere = $toWhere ?: ("to the {$dept?->name} department");
            }

            $this->notificationService->sendAppointmentNotification(
                $appointment->patient->user,
                'Appointment Referred',
                "Dr. {$referringDoctorName} has referred your appointment {$toWhere}. Reason: {$data['reason']}",
                true
            );

            $referral->load([
                'referredBy.user',
                'referredToDoctor.user',
                'referredToDepartment',
                'appointment',
            ]);

            return response()->json([
                'message' => 'Appointment referred successfully.',
                'data'    => $referral,
            ]);
        });
    }

    /**
     * New doctor accepts or rejects a referred appointment.
     * PATCH /api/appointment-referrals/{referral}/respond
     */
    public function respond(Request $request, AppointmentReferral $referral)
    {
        $user = auth()->user();

        // Only the referred-to doctor can respond
        if (! $user->hasRole('doctor') || $referral->referred_to_doctor_id !== $user->id) {
            abort(403, 'Only the referred-to doctor can respond to this referral.');
        }

        if ($referral->status !== 'pending') {
            throw ValidationException::withMessages([
                'referral' => ['This referral has already been responded to.'],
            ]);
        }

        $data = $request->validate([
            'action'           => ['required', 'in:accept,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string', 'max:1000'],
        ]);

        return DB::transaction(function () use ($referral, $data, $user) {

            $appointment = $referral->appointment;

            if ($data['action'] === 'accept') {
                $referral->update(['status' => 'accepted']);
                $appointment->update([
                    'status'      => 'confirmed',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);

                // Create a queue entry for Doctor B so the referred patient
                // appears in Doctor B's queue immediately after acceptance.
                $this->queueService->generate(
                    $referral->referred_to_doctor_id,
                    $appointment->scheduled_time->toDateString()
                );

                $this->notificationService->sendAppointmentNotification(
                    $appointment->patient->user,
                    'Referred Appointment Accepted',
                    "Dr. {$user->first_name} {$user->last_name} has accepted your referred appointment and confirmed it.",
                    true
                );
            } else {
                $referral->update([
                    'status'           => 'rejected',
                    'rejection_reason' => $data['rejection_reason'],
                ]);
                // Revert appointment back to original doctor (Doctor A) if rejected
                $appointment->update([
                    'doctor_id' => $referral->referred_by,
                    'status'    => 'pending',
                ]);

                $this->notificationService->sendAppointmentNotification(
                    $appointment->patient->user,
                    'Referred Appointment Rejected',
                    "Dr. {$user->first_name} {$user->last_name} has rejected the referral. Reason: {$data['rejection_reason']}. Your appointment is still pending with the original doctor.",
                    true
                );

                // Notify original doctor as well
                $originalDoctor = HealthcareProvider::with('user')->find($referral->referred_by);
                if ($originalDoctor?->user) {
                    $this->notificationService->createNotification([
                        'user_id'      => $originalDoctor->user->id,
                        'type'         => 'in_app',
                        'channel'      => 'appointment',
                        'reference_id' => (string) $appointment->id,
                        'subject'      => 'Referral Rejected',
                        'content'      => "Dr. {$user->first_name} {$user->last_name} rejected your referral. Reason: {$data['rejection_reason']}",
                    ]);
                }
            }

            $referral->load([
                'referredBy.user',
                'referredToDoctor.user',
                'referredToDepartment',
                'appointment',
            ]);

            return response()->json([
                'message' => 'Referral ' . $data['action'] . 'ed successfully.',
                'data'    => $referral,
            ]);
        });
    }

    /**
     * List referrals for a specific appointment.
     * GET /api/appointments/{appointment}/referrals
     */
    public function forAppointment(Appointment $appointment)
    {
        $user = auth()->user();

        // Patient can see their own, doctor can see theirs, admin sees all
        if ($user->hasRole('patient') && $appointment->patient_id !== $user->id) {
            abort(403);
        }
        if ($user->hasRole('doctor') &&
            $appointment->doctor_id !== $user->id &&
            ! AppointmentReferral::where('appointment_id', $appointment->id)
                ->where('referred_to_doctor_id', $user->id)->exists()) {
            abort(403);
        }

        $referrals = AppointmentReferral::with([
            'referredBy.user',
            'referredToDoctor.user',
            'referredToDepartment',
        ])
        ->where('appointment_id', $appointment->id)
        ->latest()
        ->get();

        return response()->json(['data' => $referrals]);
    }

    /**
     * List referrals assigned TO the authenticated doctor.
     * GET /api/appointment-referrals/incoming
     */
    public function incoming()
    {
        $user = auth()->user();
        if (! $user->hasRole('doctor')) {
            abort(403);
        }

        $referrals = AppointmentReferral::with([
            'appointment.patient.user',
            'appointment.hospital',
            'appointment.department',
            'appointment.documents',
            'referredBy.user',
            'referredToDepartment',
        ])
        ->where('referred_to_doctor_id', $user->id)
        ->where('status', 'pending')
        ->latest()
        ->get();

        return response()->json(['data' => $referrals]);
    }
}
