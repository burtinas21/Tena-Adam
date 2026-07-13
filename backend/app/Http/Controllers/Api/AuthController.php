<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(

        protected AuthServiceInterface $authService

    ) {}

    public function register(RegisterRequest $request)
    {

        $user = $this->authService
            ->register(
                $request->validated()
            );

        return response()->json([

            'message' => 'Registration successful',

            'user' => $user,

        ], 201);

    }

    public function login(LoginRequest $request)
    {

        $user = $this->authService
            ->login(
                $request->validated()
            );

        if (! $user) {

            return response()->json([

                'message' => 'Invalid credentials',

            ], 401);

        }

        $token = $user->createToken(
            'smart-care-token'
        )->plainTextToken;

        return response()->json([

            'message' => 'Login successful',

            'token' => $token,

            'user' => $user,

        ]);

    }

    public function logout(Request $request)
    {

        $this->authService
            ->logout(
                $request->user()
            );

        return response()->json([

            'message' => 'Logged out successfully',

        ]);

    }
}
