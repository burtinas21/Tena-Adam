<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'first_name' => $this->first_name,
        'last_name' => $this->last_name,
        'email' => $this->email,
        'phone' => $this->phone,
        'avatar_url' => $this->avatar_url,
        'is_active' => $this->is_active,
        'last_login' => $this->last_login,

        'roles' => $this->roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
            ];
        }),

        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
}
}
