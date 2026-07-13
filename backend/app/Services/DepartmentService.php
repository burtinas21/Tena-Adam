<?php

namespace App\Services;


use App\Models\Department;


class DepartmentService
{
 public function all()
{
    $user = auth()->user();

    if ($user->hasRole('platform_admin')) {
        return Department::with('hospital')->get();
    }

    return Department::whereIn(
        'hospital_id',
        $user->hospitals()->pluck('hospitals.id')
    )->get();
}

    public function create(array $data)
    {


        return Department::create([

            'hospital_id'
                => $data['hospital_id'],


            'name'
                => $data['name'],


            'description'
                => $data['description'] ?? null,


            'head_doctor_id'
                => $data['head_doctor_id'] ?? null,


            'parent_department_id'
                => $data['parent_department_id'] ?? null,

        ]);


    }




    public function update(
        Department $department,
        array $data
    )
    {


        $department->update($data);


        return $department;

    }




    public function delete(
        Department $department
    )
    {


        return $department->delete();


    }




    public function getByHospital(
        string $hospitalId
    )
    {


        return Department::where(
            'hospital_id',
            $hospitalId
        )
        ->get();


    }


}