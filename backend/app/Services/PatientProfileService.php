<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\PatientEmergencyContact;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PatientProfileService
{
    public function completeProfile(array $data): Patient
    {
        return DB::transaction(function () use ($data) {

            $patient = auth()->user()->patient;

            if (! $patient) {

                throw ValidationException::withMessages([
                    'patient' => [
                        'Patient profile not found.'
                    ]
                ]);

            }

            $patient->update([

                'address' => $data['address'],

                'occupation' => $data['occupation'],

                'national_id' => $data['national_id'] ?? null,

            ]);

            $this->checkProfileCompletion($patient);

            return $patient->fresh([
                'user',
                'emergencyContacts'
            ]);

        });
    }

    public function addEmergencyContact(array $data): PatientEmergencyContact
    {
        return DB::transaction(function () use ($data) {

            $patient = auth()->user()->patient;

            if (! $patient) {

                throw ValidationException::withMessages([
                    'patient' => [
                        'Patient profile not found.'
                    ]
                ]);

            }

            /*
            |--------------------------------------------------------------------------
            | Only one primary contact
            |--------------------------------------------------------------------------
            */

            if (($data['is_primary'] ?? false) === true) {

                PatientEmergencyContact::where(
                    'patient_id',
                    $patient->id
                )->update([
                    'is_primary' => false
                ]);

            }

            $contact = PatientEmergencyContact::create([

                'patient_id' => $patient->id,

                'name' => $data['name'],

                'relationship' => $data['relationship'],

                'phone' => $data['phone'],

                'email' => $data['email'] ?? null,

                'address' => $data['address'] ?? null,

                'is_primary' => $data['is_primary'] ?? false,

            ]);

            $this->checkProfileCompletion($patient);

            return $contact->fresh();

        });
    }
    public function updateEmergencyContact(
    PatientEmergencyContact $contact,
    array $data
): PatientEmergencyContact
{
    return DB::transaction(function () use ($contact, $data) {

        $patient = auth()->user()->patient;

        /*
        |--------------------------------------------------------------------------
        | Ownership check
        |--------------------------------------------------------------------------
        */

        if ($contact->patient_id !== $patient->id) {

            throw ValidationException::withMessages([
                'contact' => [
                    'Unauthorized.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | Only one primary contact
        |--------------------------------------------------------------------------
        */

        if (($data['is_primary'] ?? false) === true) {

            PatientEmergencyContact::where(
                'patient_id',
                $patient->id
            )
            ->where(
                'id',
                '!=',
                $contact->id
            )
            ->update([
                'is_primary' => false
            ]);

        }

        $contact->update($data);

        $this->checkProfileCompletion($patient);

        return $contact->fresh();
    });
}

public function deleteEmergencyContact(
    PatientEmergencyContact $contact
): bool
{
    return DB::transaction(function () use ($contact) {

        $patient = auth()->user()->patient;

        
        if ($contact->patient_id !== $patient->id) {

            throw ValidationException::withMessages([
                'contact' => [
                    'Unauthorized.'
                ]
            ]);

        }

        $contact->delete();

        $this->checkProfileCompletion($patient);

        return true;
    });
}

/*
|--------------------------------------------------------------------------
| Automatically activate patient profile
|--------------------------------------------------------------------------
*/

private function checkProfileCompletion(
    Patient $patient
): void
{
    $hasAddress = !empty(trim((string) $patient->address));

    $hasOccupation = !empty(trim((string) $patient->occupation));

    $hasPrimaryContact = $patient->emergencyContacts()
        ->where('is_primary', true)
        ->exists();

    /*
    |--------------------------------------------------------------------------
    | Activate patient
    |--------------------------------------------------------------------------
    */

    if (
        $hasAddress &&
        $hasOccupation &&
        $hasPrimaryContact
    ) {

        $patient->update([
            'patient_status' => 'active'
        ]);

    } else {

        $patient->update([
            'patient_status' => 'pending'
        ]);

    }
}
}