<?php

namespace App\Services;

use App\Models\Specialization;
use Illuminate\Support\Facades\DB;


class SpecializationService
{


    public function create(array $data)
    {

        return DB::transaction(function () use ($data) {


            return Specialization::create([

                'name' => $data['name'],

                'description' =>
                    $data['description'] ?? null,

            ]);


        });

    }





    public function update(
        Specialization $specialization,
        array $data
    )
    {

        return DB::transaction(function () use (
            $specialization,
            $data
        ) {


            $specialization->update([

                'name' =>
                    $data['name'],

                'description' =>
                    $data['description'] ?? null,

            ]);


            return $specialization->fresh();


        });


    }





    public function delete(
        Specialization $specialization
    )
    {

        return DB::transaction(function () use ($specialization) {


            return $specialization->delete();


        });

    }





    public function getAll()
    {

        return Specialization::orderBy('name')
            ->get();

    }





    public function find(
        Specialization $specialization
    )
    {

        return $specialization;

    }


}