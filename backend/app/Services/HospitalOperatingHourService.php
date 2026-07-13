<?php

namespace App\Services;


use App\Models\HospitalOperatingHour;



class HospitalOperatingHourService
{

public function all()
{
    $user = auth()->user();

    if ($user->hasRole('platform_admin')) {

        return HospitalOperatingHour::with('hospital')->get();

    }

    return HospitalOperatingHour::with('hospital')
        ->whereIn(
            'hospital_id',
            $user->hospitals()->pluck('hospitals.id')
        )
        ->get();
}

    public function create(array $data)
    {


        return HospitalOperatingHour::create([


            'hospital_id'
                => $data['hospital_id'],


            'day_of_week'
                => $data['day_of_week'],


            'open_time'
                => $data['open_time'],


            'close_time'
                => $data['close_time'],


            'is_holiday'
                => $data['is_holiday'] ?? false,


        ]);


    }





    public function update(
        HospitalOperatingHour $hour,
        array $data
    )
    {


        $hour->update($data);


        return $hour;


    }





    public function delete(
        HospitalOperatingHour $hour
    )
    {


        return $hour->delete();


    }




    public function getByHospital(
        string $hospitalId
    )
    {


        return HospitalOperatingHour::where(
            'hospital_id',
            $hospitalId
        )
        ->get();


    }


}