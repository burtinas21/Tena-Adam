<?php

namespace App\Http\Controllers\Api;

use App\Models\Specialization;
use App\Http\Controllers\Controller;

use App\Services\SpecializationService;

use App\Http\Requests\Api\Specialization\StoreSpecializationRequest;
use App\Http\Requests\Api\Specialization\UpdateSpecializationRequest;

use App\Http\Resources\SpecializationResource;


class SpecializationController extends Controller
{


    public function __construct(
        private SpecializationService $service
    ) {}




    /**
     * Display all specializations
     */
    public function index()
    {

        $this->authorize(
            'viewAny',
            Specialization::class
        );


        return SpecializationResource::collection(
            $this->service->getAll()
        );

    }






    /**
     * Store specialization
     */
    public function store(
        StoreSpecializationRequest $request
    )
    {


        $this->authorize(
            'create',
            Specialization::class
        );



        $specialization =
            $this->service->create(
                $request->validated()
            );



        return response()->json([

            'message'
                => 'Specialization created successfully',

            'data'
                => new SpecializationResource(
                    $specialization
                )

        ],201);


    }







    /**
     * Show one specialization
     */
    public function show(
        Specialization $specialization
    )
    {


        $this->authorize(
            'view',
            $specialization
        );


        return new SpecializationResource(
            $this->service->find($specialization)
        );


    }







    /**
     * Update specialization
     */
    public function update(
        UpdateSpecializationRequest $request,
        Specialization $specialization
    )
    {


        $this->authorize(
            'update',
            $specialization
        );



        $updated =
            $this->service->update(
                $specialization,
                $request->validated()
            );



        return response()->json([

            'message'
                => 'Specialization updated successfully',

            'data'
                => new SpecializationResource($updated)

        ]);

    }







    /**
     * Delete specialization
     */
    public function destroy(
        Specialization $specialization
    )
    {


        $this->authorize(
            'delete',
            $specialization
        );



        $this->service->delete(
            $specialization
        );



        return response()->json([

            'message'
                => 'Specialization deleted successfully'

        ]);

    }


}