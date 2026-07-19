<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MedicalDocument;

class MedicalDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            'platform_admin',
            'hospital_admin',
            'doctor',
            'patient',
        ]);
    }

    public function view(
        User $user,
        MedicalDocument $document
    ): bool {

        if ($user->hasRole('platform_admin')) {

            return true;

        }

        if ($user->hasRole('hospital_admin')) {

            return $user
                ->hospitalStaff()
                ->value('hospital_id')
                === $document->patient->hospital_id;

        }

        if ($user->hasRole('doctor')) {

            return true;

        }

        if ($user->hasRole('patient')) {

            return $user->id === $document->patient_id;

        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            'platform_admin',
            'hospital_admin',
            'doctor',
        ]);
    }

    public function update(
        User $user,
        MedicalDocument $document
    ): bool {

        if ($user->hasRole('platform_admin')) {

            return true;

        }

        if ($user->hasRole('hospital_admin')) {

            return true;

        }

        return $user->id === $document->uploaded_by;
    }

    public function delete(
        User $user,
        MedicalDocument $document
    ): bool {

        if ($user->hasRole('platform_admin')) {

            return true;

        }

        if ($user->hasRole('hospital_admin')) {

            return true;

        }

        return $user->id === $document->uploaded_by;
    }
}