<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        $permission
    ): Response {

        $user = auth()->user();

        if (! $user) {

            return response()->json([

                'message' => 'Unauthenticated',

            ], 401);

        }

        $hasPermission =
            $user
                ->roles()
                ->whereHas(
                    'permissions',
                    function ($query) use ($permission) {

                        $query->where(
                            'name',
                            $permission
                        );

                    }
                )
                ->exists();

        if (! $hasPermission) {

            return response()->json([

                'message' => 'You do not have permission',

            ], 403);

        }

        return $next($request);

    }
}
