<?php

namespace App\Services;
use App\Models\Report;
use App\Models\Patient;
use App\Models\ReviewRating;
use App\Models\Appointment;
use App\Models\HealthcareProvider;
use App\Models\Department;
use App\Models\TelehealthSession;
use App\Models\MedicalEncounter;
use App\Models\HospitalStaff;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Exports\ReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportService
{
    /**
     * Resolve the hospital_id for the currently authenticated user.
     * Returns null for platform_admin (they see all data).
     */
    private function getHospitalId(): ?string
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        // Platform admins see all data
        if ($user->hasRole('platform_admin')) {
            return null;
        }

        // Hospital admins are scoped to their own hospital
        return $user->hospitalStaff()
            ->where('is_active', true)
            ->value('hospital_id');
    }

    /**
 * Get patient statistics.
 *
 * Returns:
 * - Total patients
 * - Active patients
 * - Inactive patients
 * - New patients this month
 *
 * @throws ValidationException
 */
public function getPatientStatistics(): array
{
    try {

        return DB::transaction(function () {

            $hospitalId = $this->getHospitalId();

            /*
            |--------------------------------------------------------------------------
            | Base query: patients who have at least one appointment at this hospital
            |--------------------------------------------------------------------------
            */

            $baseQuery = Patient::query();

            if ($hospitalId) {
                $baseQuery->whereHas(
                    'user.appointments',
                    fn ($q) => $q->whereHas(
                        'doctor',
                        fn ($dq) => $dq->where('hospital_id', $hospitalId)
                    )
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Total Patients
            |--------------------------------------------------------------------------
            */

            $totalPatients = (clone $baseQuery)->count();

            /*
            |--------------------------------------------------------------------------
            | Active Patients
            |--------------------------------------------------------------------------
            */

            $activePatients = (clone $baseQuery)->whereHas(
                'user',
                fn ($query) => $query->where('is_active', true)
            )->count();

            /*
            |--------------------------------------------------------------------------
            | Inactive Patients
            |--------------------------------------------------------------------------
            */

            $inactivePatients = (clone $baseQuery)->whereHas(
                'user',
                fn ($query) => $query->where('is_active', false)
            )->count();

            /*
            |--------------------------------------------------------------------------
            | New Patients This Month
            |--------------------------------------------------------------------------
            */

            $newPatientsThisMonth = (clone $baseQuery)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Return Statistics
            |--------------------------------------------------------------------------
            */

            return [

                'total_patients' => $totalPatients,

                'active_patients' => $activePatients,

                'inactive_patients' => $inactivePatients,

                'new_patients_this_month' => $newPatientsThisMonth,

            ];

        });

    } catch (\Throwable $e) {

        throw ValidationException::withMessages([

            'report' => [
                'Unable to generate patient statistics.'
            ],

        ]);

    }
}
/**
 * Get appointment statistics.
 *
 * Returns:
 * - Total appointments
 * - Pending appointments
 * - Approved appointments
 * - Completed appointments
 * - Cancelled appointments
 * - Today's appointments
 * - This month's appointments
 *
 * @throws ValidationException
 */
public function getAppointmentReport(): array
{
    try {

        return DB::transaction(function () {

            $hospitalId = $this->getHospitalId();

            /*
            |--------------------------------------------------------------------------
            | Base query scoped to hospital via doctor's hospital_id
            |--------------------------------------------------------------------------
            */

            $base = Appointment::query();

            if ($hospitalId) {
                $base->whereHas(
                    'doctor',
                    fn ($q) => $q->where('hospital_id', $hospitalId)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Counts
            |--------------------------------------------------------------------------
            */

            $totalAppointments       = (clone $base)->count();
            $pendingAppointments     = (clone $base)->where('status', 'pending')->count();
            $approvedAppointments    = (clone $base)->where('status', 'approved')->count();
            $completedAppointments   = (clone $base)->where('status', 'completed')->count();
            $cancelledAppointments   = (clone $base)->where('status', 'cancelled')->count();
            $todayAppointments       = (clone $base)->whereDate('scheduled_time', Carbon::today())->count();
            $thisMonthAppointments   = (clone $base)
                ->whereMonth('scheduled_time', Carbon::now()->month)
                ->whereYear('scheduled_time', Carbon::now()->year)
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Return Report
            |--------------------------------------------------------------------------
            */

            return [

                'total_appointments'      => $totalAppointments,
                'pending_appointments'    => $pendingAppointments,
                'approved_appointments'   => $approvedAppointments,
                'completed_appointments'  => $completedAppointments,
                'cancelled_appointments'  => $cancelledAppointments,
                'today_appointments'      => $todayAppointments,
                'this_month_appointments' => $thisMonthAppointments,

            ];

        });


    } catch (\Throwable $e) {


        throw ValidationException::withMessages([

            'report' => [
                'Unable to generate appointment report.'
            ],

        ]);

    }
}
public function getDepartmentPerformance(): array
{
    try {

        $hospitalId = $this->getHospitalId();

        /*
        |--------------------------------------------------------------------------
        | Load departments (optionally scoped to hospital)
        |--------------------------------------------------------------------------
        */

        $query = Department::with(['healthcareProviders']);

        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }

        $departments = $query->get();


        return $departments->map(function ($department) {

            // Use healthcareProviders (correct relationship name)
            $doctorIds = $department
                ->healthcareProviders
                ->pluck('id');


            $appointments = Appointment::whereIn(
                'doctor_id',
                $doctorIds
            );


            $totalAppointments = (clone $appointments)
                ->count();


            $completedAppointments = (clone $appointments)
                ->where('status', 'completed')
                ->count();


            $pendingAppointments = (clone $appointments)
                ->where('status', 'pending')
                ->count();


            $cancelledAppointments = (clone $appointments)
                ->where('status', 'cancelled')
                ->count();


            $patientsServed = (clone $appointments)
                ->distinct('patient_id')
                ->count('patient_id');


            $reviews = ReviewRating::whereIn(
                'doctor_id',
                $doctorIds
            );


            $totalReviews = (clone $reviews)->count();


            $averageRating = (clone $reviews)->avg('rating');


            return [

                'department_id'
                    => $department->id,

                'department_name'
                    => $department->name,

                'total_doctors'
                    => $doctorIds->count(),

                'total_appointments'
                    => $totalAppointments,

                'completed_consultations'
                    => $completedAppointments,

                'pending_appointments'
                    => $pendingAppointments,

                'cancelled_appointments'
                    => $cancelledAppointments,

                'patients_served'
                    => $patientsServed,

                'average_doctor_workload'
                    => $doctorIds->count() > 0
                        ? round($totalAppointments / $doctorIds->count(), 2)
                        : 0,

                'total_reviews'
                    => $totalReviews,

                'average_rating'
                    => round($averageRating ?? 0, 2),

            ];


        })->toArray();


    } catch (\Exception $e) {


        throw ValidationException::withMessages([

            'report' => [

                'Unable to generate department performance report.'

            ]

        ]);

    }
}
/**
 * Get Telehealth Statistics.
 *
 * @return array
 *
 * @throws ValidationException
 */
public function getTelehealthStatistics(): array
{
    try {

        $hospitalId = $this->getHospitalId();

        /*
        |--------------------------------------------------------------------------
        | Scope telehealth sessions to hospital via appointment -> doctor
        |--------------------------------------------------------------------------
        */

        $base = TelehealthSession::query();

        if ($hospitalId) {
            $base->whereHas(
                'appointment.doctor',
                fn ($q) => $q->where('hospital_id', $hospitalId)
            );
        }

        $totalSessions     = (clone $base)->count();
        $scheduledSessions = (clone $base)->where('status', 'scheduled')->count();
        $activeSessions    = (clone $base)->where('status', 'active')->count();
        $completedSessions = (clone $base)->where('status', 'completed')->count();
        $cancelledSessions = (clone $base)->where('status', 'cancelled')->count();

        return [

            'total_sessions'     => $totalSessions,
            'scheduled_sessions' => $scheduledSessions,
            'active_sessions'    => $activeSessions,
            'completed_sessions' => $completedSessions,
            'cancelled_sessions' => $cancelledSessions,

        ];


    } catch (\Exception $e) {


        throw ValidationException::withMessages([

            'telehealth' => [

                'Failed to generate telehealth statistics.'

            ]

        ]);

    }
} 
/**
 * Get healthcare trends.
 *
 * Returns:
 * - Monthly patient registrations
 * - Monthly appointments
 * - Monthly completed consultations
 * - Monthly telehealth sessions
 *
 * @throws ValidationException
 */
public function getHealthcareTrends(): array
{
    try {

        return DB::transaction(function () {

            $hospitalId = $this->getHospitalId();

            $months = [];

            /*
            |--------------------------------------------------------------------------
            | Generate Last 12 Months
            |--------------------------------------------------------------------------
            */

            for ($i = 11; $i >= 0; $i--) {

                $date = Carbon::now()->subMonths($i);

                $months[] = [

                    'month'                  => $date->format('Y-m'),
                    'patient_registrations'  => 0,
                    'appointments'           => 0,
                    'completed_consultations'=> 0,
                    'telehealth_sessions'    => 0,

                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Monthly Statistics
            |--------------------------------------------------------------------------
            */

            foreach ($months as &$month) {

                $year        = Carbon::parse($month['month'])->year;
                $monthNumber = Carbon::parse($month['month'])->month;

                /*
                |--------------------------------------------------------------------------
                | Patient Registrations (patients who had appointments at this hospital)
                |--------------------------------------------------------------------------
                */

                $patientBase = Patient::query();

                if ($hospitalId) {
                    $patientBase->whereHas(
                        'user.appointments',
                        fn ($q) => $q->whereHas(
                            'doctor',
                            fn ($dq) => $dq->where('hospital_id', $hospitalId)
                        )
                    );
                }

                $month['patient_registrations'] = (clone $patientBase)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $monthNumber)
                    ->count();

                /*
                |--------------------------------------------------------------------------
                | Monthly Appointments
                |--------------------------------------------------------------------------
                */

                $apptBase = Appointment::query();

                if ($hospitalId) {
                    $apptBase->whereHas(
                        'doctor',
                        fn ($q) => $q->where('hospital_id', $hospitalId)
                    );
                }

                $month['appointments'] = (clone $apptBase)
                    ->whereYear('scheduled_time', $year)
                    ->whereMonth('scheduled_time', $monthNumber)
                    ->count();

                /*
                |--------------------------------------------------------------------------
                | Completed Consultations
                |--------------------------------------------------------------------------
                */

                $encounterBase = MedicalEncounter::where('status', 'completed');

                if ($hospitalId) {
                    $encounterBase->whereHas(
                        'doctor',
                        fn ($q) => $q->where('hospital_id', $hospitalId)
                    );
                }

                $month['completed_consultations'] = (clone $encounterBase)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $monthNumber)
                    ->count();

                /*
                |--------------------------------------------------------------------------
                | Telehealth Sessions
                |--------------------------------------------------------------------------
                */

                $telehealthBase = TelehealthSession::query();

                if ($hospitalId) {
                    $telehealthBase->whereHas(
                        'appointment.doctor',
                        fn ($q) => $q->where('hospital_id', $hospitalId)
                    );
                }

                $month['telehealth_sessions'] = (clone $telehealthBase)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $monthNumber)
                    ->count();

            }


            return [

                'period' => 'last_12_months',

                'trends' => $months,

            ];

        });


    } catch (\Throwable $e) {


        throw ValidationException::withMessages([

            'report' => [

                'Unable to generate healthcare trends.'

            ],

        ]);

    }
}
/**
 * Generate custom report.
 *
 * Executes saved report query and updates last_run_at.
 *
 * @throws ValidationException
 */
public function generateCustomReport(
    string $reportId,
    array $parameters = []
): array {

    try {

        return DB::transaction(function () use (
            $reportId,
            $parameters
        ) {


            /*
            |--------------------------------------------------------------------------
            | Find Report
            |--------------------------------------------------------------------------
            */

            $report = Report::findOrFail(
                $reportId
            );


            /*
            |--------------------------------------------------------------------------
            | Validate Report Status
            |--------------------------------------------------------------------------
            */

            if (!$report->is_active) {

                throw ValidationException::withMessages([

                    'report' => [
                        'This report is inactive.'
                    ]

                ]);

            }



            /*
            |--------------------------------------------------------------------------
            | Execute Report Query
            |--------------------------------------------------------------------------
            */

            $data = $this->executeReportQuery(
                $report,
                $parameters
            );



            /*
            |--------------------------------------------------------------------------
            | Update Last Run Time
            |--------------------------------------------------------------------------
            */

            $report->update([

                'last_run_at' => now()

            ]);



            /*
            |--------------------------------------------------------------------------
            | Return Report Result
            |--------------------------------------------------------------------------
            */

            return [

                'report_id' =>
                    $report->id,

                'report_name' =>
                    $report->name,


                'report_type' =>
                    $report->type,


                'generated_at' =>
                    now(),


                'data' =>
                    $data,

            ];


        });


    } catch (\Throwable $e) {


        throw ValidationException::withMessages([

            'report' => [

                'Unable to generate custom report.'

            ]

        ]);

    }
}
/**
 * Execute saved report query.
 */
private function executeReportQuery(
    Report $report,
    array $parameters = []
): array {

    try {


        /*
        |--------------------------------------------------------------------------
        | Replace parameters
        |--------------------------------------------------------------------------
        */

        $query = $report->query;



        foreach ($parameters as $key => $value) {

            $query = str_replace(

                ':' . $key,

                "'" . $value . "'",

                $query

            );

        }



        /*
        |--------------------------------------------------------------------------
        | Execute Query
        |--------------------------------------------------------------------------
        */

        return DB::select(
            $query
        );


    } catch (\Throwable $e) {


        throw ValidationException::withMessages([

            'query' => [

                'Failed to execute report query.'

            ]

        ]);

    }

}
public function createReport(array $data): Report
{
    return DB::transaction(function () use ($data) {

        return Report::create([

            'hospital_id' => auth()
                ->user()
                ->hospitalStaff()
                ->value('hospital_id'),

            'name' => $data['name'],

            'type' => $data['type'],

            'query' => $data['query'],

            'parameters' => $data['parameters'] ?? null,

            'schedule' => $data['schedule'] ?? null,

            'is_active' => true,

            'created_by' => auth()->id(),

        ]);

    });
}
public function getDoctorWorkload(): array
{
    try {

        $hospitalId = $this->getHospitalId();

        $query = HealthcareProvider::with([
            'user',
            'department',
            'hospital'
        ])
        ->withCount([
            'appointments',

            'medicalEncounters as completed_encounters_count'
                => function ($query) {
                    $query->where(
                        'medical_encounters.status',
                        'completed'
                    );
                },

            'prescriptions as active_prescriptions_count'
                => function ($query) {
                    $query->where(
                        'prescriptions.status',
                        'active'
                    );
                },

            'telehealthSessions'
        ])
        ->withAvg(
            'reviews',
            'rating'
        )
        ->withCount(
            'reviews'
        );

        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }

        $doctors = $query->get();



        return $doctors->map(function ($doctor) {


            return [

                'doctor_id' =>
                    $doctor->id,


                'doctor_name' =>
                    $doctor->user->first_name
                    .' '.
                    $doctor->user->last_name,


                'department' =>
                    $doctor->department?->name,


                'hospital' =>
                    $doctor->hospital?->name,


                'total_appointments' =>
                    $doctor->appointments_count,


                'completed_encounters' =>
                    $doctor->completed_encounters_count,


                'active_prescriptions' =>
                    $doctor->active_prescriptions_count,


                'telehealth_sessions' =>
                    $doctor->telehealth_sessions_count,


                'average_rating' =>
                    round(
                        $doctor->reviews_avg_rating ?? 0,
                        2
                    ),


                'total_reviews' =>
                    $doctor->reviews_count,

            ];


        })
        ->toArray();



    } catch (\Exception $e) {


        throw ValidationException::withMessages([

            'report' => [

                'Unable to generate doctor workload report.'

            ]

        ]);

    }
}
public function getDoctorRatingStatistics(): array
{
    try {

        $hospitalId = $this->getHospitalId();

        $query = HealthcareProvider::with(['user'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }

        $doctors = $query->get();


        return $doctors->map(function ($doctor) {


            $distribution = ReviewRating::where(
                'doctor_id',
                $doctor->id
            )
            ->selectRaw(
                'rating, COUNT(*) as total'
            )
            ->groupBy(
                'rating'
            )
            ->pluck(
                'total',
                'rating'
            );


            return [

                'doctor_id'=>$doctor->id,

                'doctor_name'=>
                    $doctor->user->first_name
                    .' '.
                    $doctor->user->last_name,


                'total_reviews'=>
                    $doctor->reviews_count,


                'average_rating'=>
                    round(
                        $doctor->reviews_avg_rating ?? 0,
                        2
                    ),


                'rating_distribution'=>$distribution

            ];


        })->toArray();


    } catch(\Exception $e){

        throw ValidationException::withMessages([

            'report'=>[
                'Unable to generate rating statistics.'
            ]

        ]);

    }
}

private function getReportData(string $type): array
{
    return match ($type) {
        'patient'     => [$this->getPatientStatistics()],
        'appointment' => [$this->getAppointmentReport()],
        'doctor'      => $this->getDoctorWorkload(),
        'department'  => $this->getDepartmentPerformance(),
        'telehealth'  => [$this->getTelehealthStatistics()],
        'trend'       => $this->getHealthcareTrends()['trends'],
        default => throw ValidationException::withMessages([
            'type' => ['Invalid report type.']
        ]),
    };
}

public function exportExcel(string $type): BinaryFileResponse
{
    $data = $this->getReportData($type);
    return Excel::download(new ReportExport($data), $type . '_report.xlsx');
}

public function exportCsv(string $type): BinaryFileResponse
{
    $data = $this->getReportData($type);
    return Excel::download(new ReportExport($data), $type . '_report.csv', ExcelFormat::CSV);
}

public function exportPdf(string $type): \Illuminate\Http\Response
{
    $data = $this->getReportData($type);
    $pdf = Pdf::loadView('reports.report', ['data' => $data]);
    return $pdf->download($type . '_report.pdf');
}
}
