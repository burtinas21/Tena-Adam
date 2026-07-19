<?php

namespace App\Providers;
use App\Models\Report;
use App\Policies\ReportPolicy;
use App\Models\Vital;
use App\Policies\VitalPolicy;
use Illuminate\Support\ServiceProvider;
use App\Models\Queue;
use App\Policies\QueuePolicy;
use App\Models\Specialization;
use App\Policies\SpecializationPolicy;
use App\Models\DoctorSchedule;
use App\Policies\DoctorSchedulePolicy;
use Illuminate\Support\Facades\Gate;
use App\Models\Appointment;
use App\Policies\AppointmentPolicy;
use App\Models\AppointmentSlot;
use App\Policies\AppointmentSlotPolicy;
use App\Models\PatientEmergencyContact;
use App\Policies\PatientEmergencyContactPolicy;
use App\Models\DoctorLeave;
use App\Models\Prescription;
use App\Policies\PrescriptionPolicy;
use App\Policies\DoctorLeavePolicy;
use App\Policies\MedicalDocumentPolicy;
use App\Models\MedicalDocument;
use App\Models\AuditLog;
use App\Policies\AuditLogPolicy;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Specialization::class => 
        SpecializationPolicy::class,

        \App\Models\Hospital::class =>
            \App\Policies\HospitalPolicy::class,

        \App\Models\Department::class =>
            \App\Policies\DepartmentPolicy::class,

        \App\Models\Facility::class =>
            \App\Policies\FacilityPolicy::class,
            Queue::class =>
            QueuePolicy::class,
            DoctorLeave::class =>
            DoctorLeavePolicy::class,
            AppointmentSlot::class =>
            AppointmentSlotPolicy::class,
        \App\Models\HospitalOperatingHour::class =>
            \App\Policies\HospitalOperatingHourPolicy::class,

        DoctorSchedule::class =>
            DoctorSchedulePolicy::class,

        \App\Models\Appointment::class =>
            \App\Policies\AppointmentPolicy::class,

        \App\Models\HealthcareProvider::class =>
            \App\Policies\HealthcareProviderPolicy::class,

        \App\Models\PatientEmergencyContact::class =>
            \App\Policies\PatientEmergencyContactPolicy::class,
            Prescription::class     => PrescriptionPolicy::class,
            Vital::class            => VitalPolicy::class,
             AuditLog::class            => AuditLogPolicy::class,
             MedicalDocument::class            => MedicalDocumentPolicy::class,
            \App\Models\MedicalEncounter::class => \App\Policies\MedicalEncounterPolicy::class,
             Report::class => ReportPolicy::class,
    \App\Models\Symptom::class => \App\Policies\SymptomPolicy::class,
    \App\Models\SymptomDepartmentMapping::class => \App\Policies\SymptomDepartmentMappingPolicy::class,
    \App\Models\SymptomAnalytic::class => \App\Policies\SymptomAnalyticsPolicy::class,
    \App\Models\TelehealthSession::class => \App\Policies\TelehealthSessionPolicy::class,
    \App\Models\TelehealthAttendance::class => \App\Policies\TelehealthAttendancePolicy::class,
    \App\Models\Notification::class => \App\Policies\NotificationPolicy::class,
    \App\Models\NotificationTemplate::class => \App\Policies\NotificationTemplatePolicy::class,

    ];



    public function register(): void
    {

    }



    public function boot(): void
    {

        foreach ($this->policies as $model => $policy) {

            Gate::policy(
                $model,
                $policy
            );

        }

    }

}