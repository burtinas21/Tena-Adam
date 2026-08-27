<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\HospitalStaff;
use App\Models\HealthcareProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class HealthcareProviderService
{


    public function create(array $data)
    {

        return DB::transaction(function () use ($data) {

            $hospitalId = auth()->user()
                ->hospitalStaff()
                ->value('hospital_id');



            if (!$hospitalId) {

                throw ValidationException::withMessages([

                    'hospital' => [
                        'Hospital admin is not assigned to a hospital.'
                    ]

                ]);

            }
            $department = Department::findOrFail(
                $data['department_id']
            );



            if ($department->hospital_id !== $hospitalId) {


                throw ValidationException::withMessages([

                    'department_id'=>[
                        'Department does not belong to your hospital.'
                    ]

                ]);

            }


            $doctor = User::create([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'email'      => $data['email'],
                'phone'      => $data['phone'] ?? null,
                'password'   => null,        // set by the doctor via invitation link
                'is_active'  => false,       // activated when they set their password
            ]);

            $doctorRole = Role::where(
                'name',
                'doctor'
            )->first();



            if(!$doctorRole){


                throw ValidationException::withMessages([

                    'role'=>[
                        'Doctor role not found.'
                    ]

                ]);

            }



            $doctor->roles()->attach(

                $doctorRole->id
            );
            HospitalStaff::create([
                'user_id'=>$doctor->id,
                'hospital_id'=>$hospitalId,
                'department_id'=>$data['department_id'],
                'position'=>'doctor',
                'is_active'=>true,
            ]);
            $imagePath = null;

if (isset($data['profile_picture'])) {

    $imagePath = $data['profile_picture']->store(
        'doctor-profiles',
        'public'
    );

}           $provider = HealthcareProvider::create([
                'id'                     => $doctor->id,
                'license_number'         => $data['license_number'],
                'department_id'          => $data['department_id'],
                'hospital_id'            => $hospitalId,
                'consultation_fee'       => $data['consultation_fee'] ?? 0,
                'years_experience'       => null,
                'practice_start_date'    => $data['practice_start_date'] ?? null,
                'bio'                    => $data['bio'] ?? null,
                'profile_picture'        => $imagePath,
                'is_telehealth_available'=> $data['is_telehealth_available'] ?? false,
            ]);

            // Generate invitation token and send activation email
            $plainToken = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $doctor->email],
                ['token' => Hash::make($plainToken), 'created_at' => now()]
            );

            \App\Models\AuditLog::create([
                'user_id'      => auth()->id(),
                'hospital_id'  => $hospitalId,
                'action'       => 'invitation_sent',
                'target_table' => 'users',
                'target_id'    => $doctor->id,
                'details'      => ['email' => $doctor->email, 'role' => 'doctor', 'hospital_id' => $hospitalId],
                'ip_address'   => request()->ip(),
                'user_agent'   => request()->userAgent(),
            ]);

            \Illuminate\Support\Facades\Mail::to($doctor->email)->send(
                new \App\Mail\StaffInvitationMail($doctor, $plainToken)
            );

            return $provider->load([
                'user',
                'department',
                'hospital',
            ]);



        });

    }
    public function update(
    HealthcareProvider $provider,
    array $data
)
{


return DB::transaction(function() use(
    $provider,
    $data
){



$user = auth()->user();



if(
    $user->hasRole('doctor')
)
{


    if($user->id !== $provider->id)
    {

        throw ValidationException::withMessages([

            'provider'=>[
                'You can only update your own profile.'
            ]

        ]);

    }

if (
    isset($data['profile_picture'])
) {

    if (
        $provider->profile_picture &&
        Storage::disk('public')->exists(
            $provider->profile_picture
        )
    ) {

        Storage::disk('public')->delete(
            $provider->profile_picture
        );

    }

    $data['profile_picture'] =
        $data['profile_picture']->store(
            'doctor-profiles',
            'public'
        );

}

    $provider->update([


        'bio'=>$data['bio'] ?? $provider->bio,


        'profile_picture'=>$data['profile_picture']
            ?? $provider->profile_picture,


        'consultation_fee'=>$data['consultation_fee']
            ?? $provider->consultation_fee,


        'is_telehealth_available'=>
            $data['is_telehealth_available']
            ?? $provider->is_telehealth_available,


    ]);



}

if(
    $user->hasRole('hospital_admin')
)
{


$hospitalId = $user
    ->hospitalStaff()
    ->value('hospital_id');



if(
    $provider->hospital_id !== $hospitalId
)
{

throw ValidationException::withMessages([

'provider'=>[
'You cannot manage another hospital doctor.'
]

]);

}





if(isset($data['department_id']))
{


$department = Department::findOrFail(
    $data['department_id']
);



if(
$department->hospital_id !== $hospitalId
)
{


throw ValidationException::withMessages([

'department_id'=>[
'Department does not belong to your hospital.'
]

]);

}


}




$provider->update($data);



}




return $provider->fresh([

'user',
'department',
'hospital'

]);



});

}







    public function delete(
        HealthcareProvider $provider
    )
    {


        return DB::transaction(function() use($provider){


            $hospitalId = auth()->user()
                ->hospitalStaff()
                ->value('hospital_id');



            if(
                $provider->hospital_id !== $hospitalId
            ){


                throw ValidationException::withMessages([

                    'provider'=>[
                        'You cannot delete another hospital doctor.'
                    ]

                ]);

            }


if (
    isset($data['profile_picture'])
) {

    if (
        $provider->profile_picture &&
        Storage::disk('public')->exists(
            $provider->profile_picture
        )
    ) {

        Storage::disk('public')->delete(
            $provider->profile_picture
        );

    }

    $data['profile_picture'] =
        $data['profile_picture']->store(
            'doctor-profiles',
            'public'
        );

}
            $provider->user->roles()->detach();


            $provider->user->hospitalStaff()->delete();


            $provider->user()->delete();



            return true;


        });


    }



}