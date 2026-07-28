<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature   = 'appointments:send-reminders';
    protected $description = 'Send 24h, 1h and 15-min reminders for upcoming telehealth appointments.';

    public function __construct(
        private NotificationService $notificationService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        // ── Windows we want to remind at ──────────────────────────────────
        // Each window is checked with ±3 min tolerance so a 5-min cron never
        // misses a boundary.
        $windows = [
            ['minutes' => 60 * 24, 'subject' => '24-Hour Reminder: Telemedicine Appointment'],
            ['minutes' => 60,      'subject' => '1-Hour Reminder: Telemedicine Appointment'],
            ['minutes' => 15,      'subject' => '15-Minute Reminder: Join Your Telemedicine Session'],
        ];

        $sent = 0;

        foreach ($windows as $window) {
            $targetMin = Carbon::now()->addMinutes($window['minutes'] - 3);
            $targetMax = Carbon::now()->addMinutes($window['minutes'] + 3);

            // Find confirmed telehealth appointments in this window
            $appointments = Appointment::with([
                'patient.user',
                'doctor.user',
                'hospital',
                'telehealthSession',
            ])
            ->where('is_telehealth', true)
            ->where('status', 'confirmed')
            ->whereBetween('scheduled_time', [$targetMin, $targetMax])
            ->get();

            foreach ($appointments as $appointment) {
                // Skip if no telehealth session yet
                if (! $appointment->telehealthSession) {
                    continue;
                }

                $session     = $appointment->telehealthSession;
                $patientUser = $appointment->patient?->user;
                $doctorUser  = $appointment->doctor?->user;

                if (! $patientUser) {
                    continue;
                }

                // Avoid sending the same reminder twice: check if one was
                // already sent for this appointment in the last 10 minutes.
                $alreadySent = \App\Models\Notification::where('user_id', $patientUser->id)
                    ->where('channel', 'telehealth')
                    ->where('reference_id', (string) $appointment->id)
                    ->where('subject', $window['subject'])
                    ->where('created_at', '>=', Carbon::now()->subMinutes(10))
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                $meetingLink = $session->session_url ?? '';
                $doctorName  = trim(
                    ($doctorUser->first_name ?? '') . ' ' . ($doctorUser->last_name ?? '')
                );
                $time = Carbon::parse($appointment->scheduled_time)
                    ->setTimezone('Africa/Addis_Ababa')
                    ->format('g:i A');
                $date = Carbon::parse($appointment->scheduled_time)
                    ->setTimezone('Africa/Addis_Ababa')
                    ->format('D, M j');

                if ($window['minutes'] === 60 * 24) {
                    $content = "You have a telemedicine appointment tomorrow with Dr. {$doctorName} at {$time} ({$date}).\n\nMeeting Link: {$meetingLink}\n\nPlease ensure your device and internet connection are ready.";
                } elseif ($window['minutes'] === 60) {
                    $content = "Your telemedicine session with Dr. {$doctorName} starts in 1 hour at {$time}.\n\nMeeting Link: {$meetingLink}\n\nPlease prepare and test your camera/microphone.";
                } else {
                    $content = "Your telemedicine session with Dr. {$doctorName} starts in 15 minutes! Click the link below to join:\n\n{$meetingLink}";
                }

                // Notify patient
                try {
                    $this->notificationService->createNotification([
                        'user_id'      => $patientUser->id,
                        'type'         => 'in_app',
                        'channel'      => 'telehealth',
                        'reference_id' => (string) $appointment->id,
                        'subject'      => $window['subject'],
                        'content'      => $content,
                    ]);
                    $sent++;
                } catch (\Throwable) { /* silent */ }

                // Also notify doctor for the 15-min window
                if ($window['minutes'] === 15 && $doctorUser) {
                    try {
                        $this->notificationService->createNotification([
                            'user_id'      => $doctorUser->id,
                            'type'         => 'in_app',
                            'channel'      => 'telehealth',
                            'reference_id' => (string) $appointment->id,
                            'subject'      => '15-Minute Reminder: Patient Session Starting Soon',
                            'content'      => "Your telemedicine session with a patient starts in 15 minutes.\n\nMeeting Link: {$meetingLink}",
                        ]);
                    } catch (\Throwable) { /* silent */ }
                }
            }
        }

        $this->info("Telehealth reminders sent: {$sent}");
        return self::SUCCESS;
    }
}
