<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\RequestsApi\Encounter\StoreMedicalEncounterRequest;
use App\Http\Requests\Api\Encounter\UpdateMedicalEncounterRequest;
// use App\Http\Requests\MedicalEncounter\CompleteEncounterRequest;
use App\Http\Resources\MedicalEncounterResource;
use App\Services\MedicalEncounterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class MedicalEncounterController extends Controller
{

    public function __construct(
        private MedicalEncounterService $service
    )
    {

    }





    /*
    |--------------------------------------------------------------------------
    | Create Medical Encounter
    |--------------------------------------------------------------------------
    |
    | POST /medical-encounters
    |
    */

    public function store(
        StoreMedicalEncounterRequest $request
    ): JsonResponse {


        $encounter =
            $this->service->createEncounter(
                $request->validated()
            );


        return response()->json([

            'message'=>'Medical encounter created successfully',

            'data'=>new MedicalEncounterResource(
                $encounter
            )

        ],201);


    }








    /*
    |--------------------------------------------------------------------------
    | Show Medical Encounter
    |--------------------------------------------------------------------------
    |
    | GET /medical-encounters/{id}
    |
    */


    public function show(
        string $id
    ): JsonResponse {


        $encounter =
            $this->service->findEncounter(
                $id
            );



        return response()->json([

            'data'=>new MedicalEncounterResource(
                $encounter
            )

        ]);

    }










    /*
    |--------------------------------------------------------------------------
    | Update Medical Encounter
    |--------------------------------------------------------------------------
    |
    | PUT /medical-encounters/{id}
    |
    */


    public function update(
        UpdateMedicalEncounterRequest $request,
        string $id
    ): JsonResponse {


        $encounter =
            $this->service->updateEncounter(
                $id,
                $request->validated()
            );



        return response()->json([


            'message'=>'Medical encounter updated successfully',


            'data'=>new MedicalEncounterResource(
                $encounter
            )


        ]);

    }









    /*
    |--------------------------------------------------------------------------
    | Complete Encounter
    |--------------------------------------------------------------------------
    |
    | POST /medical-encounters/{id}/complete
    |
    */


    public function complete(
        CompleteEncounterRequest $request,
        string $id
    ): JsonResponse {


        $encounter =
            $this->service->completeEncounter(
                $id
            );



        return response()->json([


            'message'=>'Medical consultation completed successfully',


            'data'=>new MedicalEncounterResource(
                $encounter
            )


        ]);

    }




}