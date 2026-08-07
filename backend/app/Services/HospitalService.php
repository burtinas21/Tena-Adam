<?php

namespace App\Services;

use App\Models\Hospital;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class HospitalService
{
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $hospital = Hospital::create([
                'name'                => $data['name'],
                'code'                => $data['code'] ?? null,
                'address'             => $data['address'],
                'latitude'            => $data['latitude'] ?? null,
                'longitude'           => $data['longitude'] ?? null,
                'google_place_id'     => $data['google_place_id'] ?? null,
                'city'                => $data['city'],
                'region'              => $data['region'] ?? null,
                'phone'               => $data['phone'] ?? null,
                'email'               => $data['email'] ?? null,
                'website'             => $data['website'] ?? null,
                'logo_url'            => $data['logo_url'] ?? null,
                'registration_number' => $data['registration_number'] ?? null,
            ]);

            // Automatically create hospital-scoped role copies
            $this->createScopedRoles($hospital);

            return $hospital;
        });
    }

    /**
     * Create hospital-scoped copies of manageable roles for a hospital.
     * Copies default permissions from the matching global role.
     * Safe to call multiple times (idempotent).
     */
    public function createScopedRoles(Hospital $hospital): void
    {
        $scopedRoleNames = ['doctor', 'receptionist', 'patient', 'hospital_admin'];

        foreach ($scopedRoleNames as $roleName) {
            // Skip if already exists for this hospital
            $exists = Role::where('name', $roleName)
                ->where('hospital_id', $hospital->id)
                ->exists();

            if ($exists) {
                continue;
            }

            // Get the global role to copy description + permissions from
            $globalRole = Role::whereNull('hospital_id')
                ->where('name', $roleName)
                ->first();

            $scopedRole = Role::create([
                'name'        => $roleName,
                'description' => $globalRole?->description ?? null,
                'hospital_id' => $hospital->id,
                'is_default'  => false,
            ]);

            // Copy default permissions from global role
            if ($globalRole) {
                $permissionIds = $globalRole->permissions->pluck('id')->toArray();
                $scopedRole->permissions()->sync($permissionIds);
            }
        }
    }

    public function update(Hospital $hospital, array $data)
    {
        $hospital->update($data);
        return $hospital;
    }

    public function delete(Hospital $hospital)
    {
        return $hospital->delete();
    }

    public function all()
    {
        $user = auth()->user();

        // Platform admin and patients see all hospitals
        if ($user->hasRole('platform_admin') || $user->hasRole('patient')) {
            return Hospital::with([
                'departments.healthcareProviders',
                'facilities',
            ])->get();
        }

        return $user->hospitals()->with([
            'departments',
            'facilities',
        ])->get();
    }

    public function find(string $id)
    {
        return Hospital::findOrFail($id);
    }
}
