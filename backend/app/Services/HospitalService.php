<?php

namespace App\Services;


use App\Models\Hospital;
use Illuminate\Support\Facades\DB;


class HospitalService
{


    public function create(array $data)
    {

        return DB::transaction(function () use ($data) {


            return Hospital::create([

                'name' => $data['name'],

                'code' => $data['code'] ?? null,

                'address' => $data['address'],

                'city' => $data['city'],

                'region' => $data['region'] ?? null,

                'phone' => $data['phone'] ?? null,

                'email' => $data['email'] ?? null,

                'website' => $data['website'] ?? null,

                'logo_url' => $data['logo_url'] ?? null,

                'registration_number'
                    => $data['registration_number'] ?? null,

            ]);


        });


    }



    public function update(
        Hospital $hospital,
        array $data
    )
    {


        $hospital->update($data);


        return $hospital;

    }



    public function delete(Hospital $hospital)
    {

        return $hospital->delete();

    }



   public function all()
{
    $user = auth()->user();

    // Platform admin and patients see all hospitals
    if ($user->hasRole('platform_admin') || $user->hasRole('patient')) {
        return Hospital::with([
            'departments.healthcareProviders',
            'facilities',
        ])->get();
    }

    
    return $user->hospitals()->with([
        'departments',
        'facilities',
    ])->get();
}



    public function find(string $id)
    {

        return Hospital::findOrFail($id);

    }


}