<?php

namespace App\Services;


use App\Models\Facility;


class FacilityService
{
public function all()
{
    $user = auth()->user();

    if ($user->hasRole('platform_admin')) {

        return Facility::with('hospital')->get();

    }

    return Facility::with('hospital')
        ->whereIn(
            'hospital_id',
            $user->hospitals()->pluck('hospitals.id')
        )
        ->get();
}

    public function create(array $data)
    {


        return Facility::create([

            'hospital_id'
                => $data['hospital_id'],


            'name'
                => $data['name'],


            'type'
                => $data['type'],


            'status'
                => $data['status'] ?? 'available',


            'description'
                => $data['description'] ?? null,

        ]);


    }





    public function update(
        Facility $facility,
        array $data
    )
    {


        $facility->update($data);


        return $facility;


    }




    public function delete(
        Facility $facility
    )
    {


        return $facility->delete();


    }




    public function getByHospital(
        string $hospitalId
    )
    {


        return Facility::where(
            'hospital_id',
            $hospitalId
        )
        ->get();


    }


}