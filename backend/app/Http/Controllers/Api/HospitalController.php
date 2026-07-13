<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Hospital\StoreHospitalRequest;
use App\Http\Requests\Api\Hospital\UpdateHospitalRequest;
use App\Http\Resources\HospitalResource;
use App\Models\Hospital;
use App\Services\HospitalService;


class HospitalController extends Controller
{

    public function __construct(
        protected HospitalService $hospitalService
    ){}


    public function index()
    {

        $this->authorize(
            'viewAny',
            Hospital::class
        );


        return HospitalResource::collection(
            $this->hospitalService->all()
        );

    }



    public function store(
        StoreHospitalRequest $request
    )
    {

        $this->authorize(
            'create',
            Hospital::class
        );


        $hospital =
            $this->hospitalService->create(
                $request->validated()
            );


        return new HospitalResource($hospital);

    }




    public function show(Hospital $hospital)
    {

        $this->authorize(
            'view',
            $hospital
        );


        return new HospitalResource(

            $hospital->load([
                'departments.healthcareProviders',
                'facilities'
            ])

        );

    }





    public function update(
        UpdateHospitalRequest $request,
        Hospital $hospital
    )
    {

        $this->authorize(
            'update',
            $hospital
        );


        $hospital =
            $this->hospitalService->update(
                $hospital,
                $request->validated()
            );


        return new HospitalResource($hospital);

    }




    public function destroy(Hospital $hospital)
    {

        $this->authorize(
            'delete',
            $hospital
        );


        $this->hospitalService
            ->delete($hospital);


        return response()->json([

            'message'
            =>
            'Hospital deleted successfully'

        ]);

    }

}