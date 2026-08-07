<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\Department;
use App\Models\DoctorLeave;
use App\Models\DoctorSchedule;
use App\Models\Facility;
use App\Models\HealthcareProvider;
use App\Models\Hospital;
use App\Models\HospitalOperatingHour;
use App\Models\HospitalStaff;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Role;
use App\Models\User;
use App\Services\HospitalService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EthiopiaHospitalSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $hospitalService = new HospitalService();

            // ================================================================
            // 1. HOSPITALS  (use service so scoped roles are auto-created)
            // ================================================================

            $hospitalA = $hospitalService->create([
                'name'                => 'Tikur Anbessa Specialized Hospital',
                'code'                => 'TASH-001',
                'address'             => 'Lideta Sub-city, Addis Ababa',
                'city'                => 'Addis Ababa',
                'region'              => 'Addis Ababa City Administration',
                'phone'               => '+251-11-551-7011',
                'email'               => 'info@tash.gov.et',
                'website'             => 'https://www.tash.gov.et',
                'registration_number' => 'MOH-ETH-2001',
                'is_active'           => true,
                'latitude'            => 9.019287049008764,
                'longitude'           => 38.74875231172921,
            ]);

            $hospitalB = $hospitalService->create([
                'name'                => 'St. Paul\'s Hospital Millennium Medical College',
                'code'                => 'SPHMMC-002',
                'address'             => 'Gulele Sub-city, Addis Ababa',
                'city'                => 'Addis Ababa',
                'region'              => 'Addis Ababa City Administration',
                'phone'               => '+251-11-275-3722',
                'email'               => 'contact@sphmmc.edu.et',
                'website'             => 'https://www.sphmmc.edu.et',
                'registration_number' => 'MOH-ETH-2002',
                'is_active'           => true,
                'latitude'            => 9.048841379598851,
                'longitude'           => 38.729461441912484,
            ]);

            // ================================================================
            // 2. OPERATING HOURS  (Sun=0 … Sat=6, Fri/Sat closed)
            // ================================================================

            $workDays = [
                ['day' => 0, 'open' => '08:00', 'close' => '17:00', 'holiday' => false], // Sun
                ['day' => 1, 'open' => '08:00', 'close' => '17:00', 'holiday' => false], // Mon
                ['day' => 2, 'open' => '08:00', 'close' => '17:00', 'holiday' => false], // Tue
                ['day' => 3, 'open' => '08:00', 'close' => '17:00', 'holiday' => false], // Wed
                ['day' => 4, 'open' => '08:00', 'close' => '17:00', 'holiday' => false], // Thu
                ['day' => 5, 'open' => '08:00', 'close' => '12:00', 'holiday' => false], // Fri (half day)
                ['day' => 6, 'open' => '00:00', 'close' => '00:00', 'holiday' => true],  // Sat (closed)
            ];

            foreach ([$hospitalA, $hospitalB] as $hospital) {
                foreach ($workDays as $wd) {
                    HospitalOperatingHour::create([
                        'hospital_id' => $hospital->id,
                        'day_of_week' => $wd['day'],
                        'open_time'   => $wd['open'],
                        'close_time'  => $wd['close'],
                        'is_holiday'  => $wd['holiday'],
                    ]);
                }
            }

            // ================================================================
            // 3. DEPARTMENTS
            // ================================================================

            $deptData = [
                ['name' => 'Internal Medicine',       'desc' => 'Diagnosis and non-surgical treatment of adult diseases'],
                ['name' => 'Surgery',                 'desc' => 'General and specialist surgical services'],
                ['name' => 'Pediatrics',              'desc' => 'Medical care for infants, children and adolescents'],
                ['name' => 'Obstetrics & Gynecology', 'desc' => 'Women health, maternity and reproductive services'],
                ['name' => 'Cardiology',              'desc' => 'Heart and cardiovascular system disorders'],
                ['name' => 'Orthopedics',             'desc' => 'Bone, joint and musculoskeletal conditions'],
                ['name' => 'Ophthalmology',           'desc' => 'Eye diseases and vision care'],
                ['name' => 'Psychiatry',              'desc' => 'Mental health and behavioral disorders'],
            ];

            $deptsA = [];
            $deptsB = [];

            foreach ($deptData as $d) {
                $deptsA[] = Department::create([
                    'hospital_id' => $hospitalA->id,
                    'name'        => $d['name'],
                    'description' => $d['desc'],
                    'is_active'   => true,
                ]);
                $deptsB[] = Department::create([
                    'hospital_id' => $hospitalB->id,
                    'name'        => $d['name'],
                    'description' => $d['desc'],
                    'is_active'   => true,
                ]);
            }

         
            $facilitiesData = [
                ['name' => 'Consultation Room 1',     'type' => 'room',     'status' => 'available'],
                ['name' => 'Consultation Room 2',     'type' => 'room',     'status' => 'available'],
                ['name' => 'Operating Theatre 1',     'type' => 'room',     'status' => 'available'],
                ['name' => 'ICU Bed 1',               'type' => 'bed',      'status' => 'available'],
                ['name' => 'ICU Bed 2',               'type' => 'bed',      'status' => 'occupied'],
                ['name' => 'General Ward Bed 1',      'type' => 'bed',      'status' => 'available'],
                ['name' => 'Laboratory',              'type' => 'lab',      'status' => 'available'],
                ['name' => 'Radiology Lab',           'type' => 'lab',      'status' => 'available'],
                ['name' => 'Pharmacy',                'type' => 'pharmacy', 'status' => 'available'],
                ['name' => 'Outpatient Clinic',       'type' => 'clinic',   'status' => 'available'],
            ];

            foreach ([$hospitalA, $hospitalB] as $hospital) {
                foreach ($facilitiesData as $f) {
                    Facility::create([
                        'hospital_id' => $hospital->id,
                        'name'        => $f['name'],
                        'type'        => $f['type'],
                        'status'      => $f['status'],
                        'description' => $f['name'] . ' at ' . $hospital->name,
                    ]);
                }
            }

            // ================================================================
            // 5. HOSPITAL ADMIN USERS
            // ================================================================

            // Global role for attaching to the user (user_roles pivot uses global role id)
            $hospitalAdminRole = Role::whereNull('hospital_id')->where('name', 'hospital_admin')->first();
            $doctorRole        = Role::whereNull('hospital_id')->where('name', 'doctor')->first();
            $patientRole       = Role::whereNull('hospital_id')->where('name', 'patient')->first();

            $adminA = User::create([
                'first_name' => 'Belay',
                'last_name'  => 'Tadesse',
                'email'      => 'belay.tadesse@tash.gov.et',
                'phone'      => '+251911234501',
                'password'   => Hash::make('password'),
                'is_active'  => true,
            ]);
            $adminA->roles()->attach($hospitalAdminRole->id, ['assigned_by' => $adminA->id]);
            HospitalStaff::create([
                'user_id'     => $adminA->id,
                'hospital_id' => $hospitalA->id,
                'position'    => 'hospital_admin',
                'hire_date'   => '2020-01-15',
                'is_active'   => true,
            ]);

            $adminB = User::create([
                'first_name' => 'Meron',
                'last_name'  => 'Haile',
                'email'      => 'meron.haile@sphmmc.edu.et',
                'phone'      => '+251911234502',
                'password'   => Hash::make('password'),
                'is_active'  => true,
            ]);
            $adminB->roles()->attach($hospitalAdminRole->id, ['assigned_by' => $adminB->id]);
            HospitalStaff::create([
                'user_id'     => $adminB->id,
                'hospital_id' => $hospitalB->id,
                'position'    => 'hospital_admin',
                'hire_date'   => '2019-05-01',
                'is_active'   => true,
            ]);
            $doctorsRaw = [
                // Hospital A doctors
                [
                    'hospital'   => $hospitalA,
                    'dept_index' => 0, // Internal Medicine
                    'first_name' => 'Abebe',
                    'last_name'  => 'Girma',
                    'email'      => 'abebe.girma@tash.gov.et',
                    'phone'      => '+251911300101',
                    'license'    => 'ETH-MED-10001',
                    'fee'        => 300.00,
                    'bio'        => 'Specialist in internal medicine with 12 years of experience.',
                    'start_date' => '2012-09-01',
                    'telehealth' => true,
                ],
                [
                    'hospital'   => $hospitalA,
                    'dept_index' => 1, // Surgery
                    'first_name' => 'Tigist',
                    'last_name'  => 'Bekele',
                    'email'      => 'tigist.bekele@tash.gov.et',
                    'phone'      => '+251911300102',
                    'license'    => 'ETH-MED-10002',
                    'fee'        => 500.00,
                    'bio'        => 'General surgeon specializing in laparoscopic procedures.',
                    'start_date' => '2014-03-15',
                    'telehealth' => false,
                ],
                [
                    'hospital'   => $hospitalA,
                    'dept_index' => 2, // Pediatrics
                    'first_name' => 'Dawit',
                    'last_name'  => 'Alemu',
                    'email'      => 'dawit.alemu@tash.gov.et',
                    'phone'      => '+251911300103',
                    'license'    => 'ETH-MED-10003',
                    'fee'        => 250.00,
                    'bio'        => 'Pediatrician with focus on neonatal care.',
                    'start_date' => '2016-07-01',
                    'telehealth' => true,
                ],
                [
                    'hospital'   => $hospitalA,
                    'dept_index' => 4, 
                    'first_name' => 'Hiwot',
                    'last_name'  => 'Solomon',
                    'email'      => 'hiwot.solomon@tash.gov.et',
                    'phone'      => '+251911300104',
                    'license'    => 'ETH-MED-10004',
                    'fee'        => 600.00,
                    'bio'        => 'Interventional cardiologist with 15 years of practice.',
                    'start_date' => '2009-01-10',
                    'telehealth' => false,
                ],
    
                [
                    'hospital'   => $hospitalB,
                    'dept_index' => 0, // Internal Medicine
                    'first_name' => 'Yonas',
                    'last_name'  => 'Tesfaye',
                    'email'      => 'yonas.tesfaye@sphmmc.edu.et',
                    'phone'      => '+251911300201',
                    'license'    => 'ETH-MED-20001',
                    'fee'        => 280.00,
                    'bio'        => 'Internal medicine physician experienced in infectious diseases.',
                    'start_date' => '2015-02-01',
                    'telehealth' => true,
                ],
                [
                    'hospital'   => $hospitalB,
                    'dept_index' => 3, // OB/GYN
                    'first_name' => 'Selamawit',
                    'last_name'  => 'Negash',
                    'email'      => 'selamawit.negash@sphmmc.edu.et',
                    'phone'      => '+251911300202',
                    'license'    => 'ETH-MED-20002',
                    'fee'        => 400.00,
                    'bio'        => 'Obstetrician and gynecologist with expertise in high-risk pregnancies.',
                    'start_date' => '2013-06-01',
                    'telehealth' => false,
                ],
                [
                    'hospital'   => $hospitalB,
                    'dept_index' => 5, // Orthopedics
                    'first_name' => 'Mulugeta',
                    'last_name'  => 'Worku',
                    'email'      => 'mulugeta.worku@sphmmc.edu.et',
                    'phone'      => '+251911300203',
                    'license'    => 'ETH-MED-20003',
                    'fee'        => 450.00,
                    'bio'        => 'Orthopedic surgeon specializing in trauma and joint replacement.',
                    'start_date' => '2011-09-15',
                    'telehealth' => false,
                ],
                [
                    'hospital'   => $hospitalB,
                    'dept_index' => 7, // Psychiatry
                    'first_name' => 'Rahel',
                    'last_name'  => 'Desta',
                    'email'      => 'rahel.desta@sphmmc.edu.et',
                    'phone'      => '+251911300204',
                    'license'    => 'ETH-MED-20004',
                    'fee'        => 350.00,
                    'bio'        => 'Psychiatrist with expertise in anxiety and mood disorders.',
                    'start_date' => '2017-04-01',
                    'telehealth' => true,
                ],
            ];

            $doctorModels = [];

            foreach ($doctorsRaw as $dr) {
                $isHospitalA = $dr['hospital']->id === $hospitalA->id;
                $depts       = $isHospitalA ? $deptsA : $deptsB;
                $dept        = $depts[$dr['dept_index']];

                // Create user
                $user = User::create([
                    'first_name' => $dr['first_name'],
                    'last_name'  => $dr['last_name'],
                    'email'      => $dr['email'],
                    'phone'      => $dr['phone'],
                    'password'   => Hash::make('password'),
                    'is_active'  => true,
                ]);
                $user->roles()->attach($doctorRole->id, ['assigned_by' => $user->id]);

                // Create healthcare provider (same UUID as user)
                $provider = HealthcareProvider::create([
                    'id'                      => $user->id,
                    'license_number'          => $dr['license'],
                    'department_id'           => $dept->id,
                    'hospital_id'             => $dr['hospital']->id,
                    'consultation_fee'        => $dr['fee'],
                    'bio'                     => $dr['bio'],
                    'is_verified'             => true,
                    'is_telehealth_available' => $dr['telehealth'],
                    'practice_start_date'     => $dr['start_date'],
                ]);

                $doctorModels[] = $provider;

                // Hospital staff record
                HospitalStaff::create([
                    'user_id'       => $user->id,
                    'hospital_id'   => $dr['hospital']->id,
                    'department_id' => $dept->id,
                    'position'      => 'doctor',
                    'hire_date'     => $dr['start_date'],
                    'is_active'     => true,
                ]);
            }

            // ================================================================
            // 7. DOCTOR SCHEDULES  (Mon–Fri work days, day_of_week 1–5)
            // ================================================================

            foreach ($doctorModels as $provider) {
                foreach ([1, 2, 3, 4, 5] as $day) {
                    DoctorSchedule::create([
                        'doctor_id'        => $provider->id,
                        'day_of_week'      => $day,
                        'start_time'       => '08:30',
                        'end_time'         => '16:30',
                        'slot_duration_min'=> 30,
                        'lunch_start'      => '12:00',
                        'lunch_end'        => '13:00',
                        'is_available'     => true,
                    ]);
                }
            }

            // ================================================================
            // 8. DOCTOR LEAVES
            // ================================================================

            $leaveTypes = ['vacation', 'sick', 'training', 'other'];

            foreach (array_slice($doctorModels, 0, 4) as $i => $provider) {
                DoctorLeave::create([
                    'doctor_id'   => $provider->id,
                    'leave_date'  => Carbon::now()->addDays(30 + $i * 7)->toDateString(),
                    'reason'      => 'Annual leave — ' . $leaveTypes[$i],
                    'leave_type'  => $leaveTypes[$i],
                    'status'      => 'approved',
                    'approved_by' => null,
                ]);
            }

            // ================================================================
            // 9. PATIENTS
            // ================================================================

            $patientsRaw = [
                ['first_name' => 'Biruk',      'last_name' => 'Tesfaye',  'email' => 'biruk.tesfaye@gmail.com',  'phone' => '+251912000001', 'dob' => '1990-03-12', 'gender' => 'Male',   'blood' => 'O+', 'address' => 'Bole Sub-city, Addis Ababa'],
                ['first_name' => 'Frehiwot',   'last_name' => 'Kebede',   'email' => 'frehiwot.k@gmail.com',     'phone' => '+251912000002', 'dob' => '1985-07-25', 'gender' => 'Female', 'blood' => 'A+', 'address' => 'Yeka Sub-city, Addis Ababa'],
                ['first_name' => 'Samuel',     'last_name' => 'Wondimu',  'email' => 'samuel.wondimu@gmail.com', 'phone' => '+251912000003', 'dob' => '1995-11-08', 'gender' => 'Male',   'blood' => 'B+', 'address' => 'Kirkos Sub-city, Addis Ababa'],
                ['first_name' => 'Lidiya',     'last_name' => 'Habtamu',  'email' => 'lidiya.h@yahoo.com',       'phone' => '+251912000004', 'dob' => '2000-01-30', 'gender' => 'Female', 'blood' => 'AB-','address' => 'Arada Sub-city, Addis Ababa'],
                ['first_name' => 'Getachew',   'last_name' => 'Mekonen',  'email' => 'getachew.m@gmail.com',     'phone' => '+251912000005', 'dob' => '1978-06-15', 'gender' => 'Male',   'blood' => 'O-', 'address' => 'Nifas Silk Lafto, Addis Ababa'],
                ['first_name' => 'Aziza',      'last_name' => 'Mahmoud',  'email' => 'aziza.mahmoud@gmail.com',  'phone' => '+251912000006', 'dob' => '1992-09-22', 'gender' => 'Female', 'blood' => 'A-', 'address' => 'Addis Ketema, Addis Ababa'],
                ['first_name' => 'Henok',      'last_name' => 'Gebreyes', 'email' => 'henok.g@gmail.com',        'phone' => '+251912000007', 'dob' => '1988-04-05', 'gender' => 'Male',   'blood' => 'B-', 'address' => 'Lideta Sub-city, Addis Ababa'],
                ['first_name' => 'Yeshi',      'last_name' => 'Desta',    'email' => 'yeshi.desta@gmail.com',    'phone' => '+251912000008', 'dob' => '2003-12-17', 'gender' => 'Female', 'blood' => 'O+', 'address' => 'Gulele Sub-city, Addis Ababa'],
            ];

            $patientModels = [];

            foreach ($patientsRaw as $pr) {
                $pUser = User::create([
                    'first_name' => $pr['first_name'],
                    'last_name'  => $pr['last_name'],
                    'email'      => $pr['email'],
                    'phone'      => $pr['phone'],
                    'password'   => Hash::make('password'),
                    'is_active'  => true,
                ]);
                $pUser->roles()->attach($patientRole->id, ['assigned_by' => $pUser->id]);

                $patient = Patient::create([
                    'id'             => $pUser->id,
                    'date_of_birth'  => $pr['dob'],
                    'gender'         => $pr['gender'],
                    'blood_type'     => $pr['blood'],
                    'address'        => $pr['address'],
                    'patient_status' => 'active',
                    'registered_by'  => $adminA->id,
                ]);

                $patientModels[] = ['user' => $pUser, 'patient' => $patient];
            }

            // ================================================================
            // 10. APPOINTMENTS + SLOTS
            // ================================================================

            // First Mon after today
            $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();

            $appointmentsRaw = [
                ['patient_idx' => 0, 'doctor_idx' => 0, 'time' => '09:00', 'reason' => 'Persistent headache and fatigue for 2 weeks',     'status' => 'confirmed'],
                ['patient_idx' => 1, 'doctor_idx' => 0, 'time' => '09:30', 'reason' => 'Follow-up for hypertension management',           'status' => 'pending'],
                ['patient_idx' => 2, 'doctor_idx' => 1, 'time' => '10:00', 'reason' => 'Abdominal pain assessment',                       'status' => 'confirmed'],
                ['patient_idx' => 3, 'doctor_idx' => 2, 'time' => '08:30', 'reason' => 'Child vaccination and growth check',              'status' => 'pending'],
                ['patient_idx' => 4, 'doctor_idx' => 3, 'time' => '11:00', 'reason' => 'Chest pain and shortness of breath',              'status' => 'confirmed'],
                ['patient_idx' => 5, 'doctor_idx' => 4, 'time' => '09:00', 'reason' => 'Diabetes management follow-up',                   'status' => 'pending'],
                ['patient_idx' => 6, 'doctor_idx' => 5, 'time' => '10:30', 'reason' => 'Irregular menstrual cycle consultation',          'status' => 'pending'],
                ['patient_idx' => 7, 'doctor_idx' => 6, 'time' => '09:30', 'reason' => 'Knee pain after sports injury',                   'status' => 'confirmed'],
            ];

            $appointmentModels = [];

            foreach ($appointmentsRaw as $ar) {
                $pUser    = $patientModels[$ar['patient_idx']]['user'];
                $provider = $doctorModels[$ar['doctor_idx']];

                $slotStart = Carbon::parse($nextMonday . ' ' . $ar['time']);
                $slotEnd   = (clone $slotStart)->addMinutes(30);

                $slot = AppointmentSlot::create([
                    'doctor_id'  => $provider->id,
                    'start_time' => $slotStart,
                    'end_time'   => $slotEnd,
                    'status'     => 'booked',
                ]);

                $appointment = Appointment::create([
                    'patient_id'     => $pUser->id,
                    'doctor_id'      => $provider->id,
                    'hospital_id'    => $provider->hospital_id,
                    'department_id'  => $provider->department_id,
                    'slot_id'        => $slot->id,
                    'scheduled_time' => $slotStart,
                    'duration_min'   => 30,
                    'status'         => $ar['status'],
                    'reason'         => $ar['reason'],
                    'notes'          => null,
                    'is_telehealth'  => false,
                ]);

                $appointmentModels[] = $appointment;
            }

            // ================================================================
            // 11. QUEUE  — 3 entries per doctor so all operations can be tested
            // ================================================================

            $queueDate = $nextMonday;

            // appointment-based entries (one per doctor from appointments)
            $apptByDoctor = [];
            foreach ($appointmentModels as $appt) {
                $apptByDoctor[(string) $appt->doctor_id][] = $appt;
            }

            foreach ($doctorModels as $provider) {
                $doctorAppts = $apptByDoctor[(string) $provider->id] ?? [];

                // Seed up to 2 appointment-based entries per doctor
                $qNum = 1;
                foreach (array_slice($doctorAppts, 0, 2) as $appt) {
                    Queue::create([
                        'id'             => Str::uuid()->toString(),
                        'appointment_id' => $appt->id,
                        'doctor_id'      => $provider->id,
                        'hospital_id'    => $provider->hospital_id,
                        'queue_date'     => $queueDate,
                        'queue_number'   => $qNum++,
                        'status'         => 'waiting',
                    ]);
                }

                // Always add at least one walk-in so every doctor has ≥ 3 entries
                $walkInNames = [
                    'Alemu Bekele', 'Tigist Worku', 'Mohammed Seid',
                    'Meseret Alemu', 'Tesfaye Hailu', 'Zerihun Abebe',
                    'Hana Girma', 'Abreham Tadesse',
                ];
                $idx = array_search($provider, $doctorModels);

                // Fill to ensure 3 waiting entries per doctor
                $remaining = max(0, 3 - ($qNum - 1));
                for ($i = 0; $i < $remaining; $i++) {
                    Queue::create([
                        'id'                   => Str::uuid()->toString(),
                        'appointment_id'       => null,
                        'doctor_id'            => $provider->id,
                        'hospital_id'          => $provider->hospital_id,
                        'queue_date'           => $queueDate,
                        'queue_number'         => $qNum++,
                        'status'               => 'waiting',
                        'walk_in_patient_name' => $walkInNames[($idx + $i) % count($walkInNames)],
                        'walk_in_phone'        => '+25191210' . str_pad($idx * 10 + $i, 4, '0', STR_PAD_LEFT),
                    ]);
                }
            }

        }); // end transaction
    }
}
