<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Return null for API routes so Laravel never tries to redirect
     * to a named "login" route (which doesn't exist in this API-only app).
     * Returning null causes the framework to throw AuthenticationException,
     * which is then caught and rendered as a 401 JSON response.
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}
