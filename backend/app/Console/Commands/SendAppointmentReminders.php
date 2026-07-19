<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Send reminders for appointments scheduled tomorrow.';

    public function __construct(
        private NotificationService $notificationService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $appointments = Appointment::with([
            'patient.user.notificationPreference',
            'doctor.user',
            'hospital',
        ])
        ->whereDate('appointment_date', $tomorrow)
        ->where('status', 'approved')
        ->get();

        foreach ($appointments as $appointment) {

            $preference = $appointment
                ->patient
                ->user
                ->notificationPreference;

            if (
                $preference &&
                $preference->appointment_reminders &&
                $preference->email_enabled
            ) {

                $this->notificationService
                    ->sendAppointmentReminder(
                        $appointment
                    );

            }

        }

        $this->info(
            'Appointment reminders sent successfully.'
        );

        return self::SUCCESS;
    }
}