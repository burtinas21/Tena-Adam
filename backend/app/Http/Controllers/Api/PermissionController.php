<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * List all permissions, grouped by module.
     * Both platform_admin and hospital_admin can view permissions.
     * Hospital admins see all except they cannot grant delete_hospitals.
     */
    public function index(Request $request)
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();

        // Group by module for easier frontend rendering
        $grouped = $permissions->groupBy('module')->map(function ($items, $module) {
            return [
                'module'      => $module,
                'permissions' => $items->values(),
            ];
        })->values();

        return response()->json([
            'data'    => $permissions,
            'grouped' => $grouped,
        ]);
    }
}
