<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MedicalDocument;

class MedicalDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_emr');
    }

    public function view(User $user, MedicalDocument $document): bool
    {
        if (!$user->hasPermission('view_emr')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($user->hasRole('hospital_admin')) {
            return $user->hospitalStaff()->value('hospital_id')
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
        return $user->hasPermission('create_emr');
    }

    public function update(User $user, MedicalDocument $document): bool
    {
        if (!$user->hasPermission('update_emr')) {
            return false;
        }

        if ($user->hasRole('platform_admin') || $user->hasRole('hospital_admin')) {
            return true;
        }

        return $user->id === $document->uploaded_by;
    }

    public function delete(User $user, MedicalDocument $document): bool
    {
        if (!$user->hasPermission('update_emr')) {
            return false;
        }

        if ($user->hasRole('platform_admin') || $user->hasRole('hospital_admin')) {
            return true;
        }

        return $user->id === $document->uploaded_by;
    }
}
