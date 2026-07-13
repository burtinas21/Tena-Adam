<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Patient\CompletePatientProfileRequest;
use App\Http\Requests\Api\Patient\StoreEmergencyContactRequest;
use App\Http\Requests\Api\Patient\UpdateEmergencyContactRequest;
use App\Models\PatientEmergencyContact;
use App\Services\PatientProfileService;

class PatientProfileController extends Controller
{
    public function __construct(
        private PatientProfileService $service
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Complete Patient Profile
    |--------------------------------------------------------------------------
    */

    public function completeProfile(
        CompletePatientProfileRequest $request
    ) {

        $patient = $this->service->completeProfile(
            $request->validated()
        );

        return response()->json([

            'message' => 'Profile updated successfully.',

            'data' => $patient,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Add Emergency Contact
    |--------------------------------------------------------------------------
    */

    public function storeEmergencyContact(
        StoreEmergencyContactRequest $request
    ) {

        $contact = $this->service->addEmergencyContact(
            $request->validated()
        );

        return response()->json([

            'message' => 'Emergency contact added successfully.',

            'data' => $contact,

        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Emergency Contact
    |--------------------------------------------------------------------------
    */

    public function updateEmergencyContact(
        UpdateEmergencyContactRequest $request,
        PatientEmergencyContact $contact
    ) {

        $this->authorize('update', $contact);

        $contact = $this->service->updateEmergencyContact(
            $contact,
            $request->validated()
        );

        return response()->json([

            'message' => 'Emergency contact updated successfully.',

            'data' => $contact,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Emergency Contact
    |--------------------------------------------------------------------------
    */

    public function deleteEmergencyContact(
        PatientEmergencyContact $contact
    ) {

        $this->authorize('delete', $contact);

        $this->service->deleteEmergencyContact($contact);

        return response()->json([

            'message' => 'Emergency contact deleted successfully.'

        ]);
    }
}