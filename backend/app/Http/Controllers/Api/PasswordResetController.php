<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function forgotPassword(
        ForgotPasswordRequest $request
    ) {

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {

            return response()->json([

                'message' => 'Password reset link sent',

            ]);

        }

        return response()->json([

            'message' => 'Unable to send reset link',

        ], 400);

    }

    public function resetPassword(
        ResetPasswordRequest $request
    ) {

        $status = Password::reset(

            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),

            function (
                User $user,
                string $password
            ) {

                $user->update([

                    'password' => Hash::make($password),

                ]);

            }

        );

        if ($status === Password::PASSWORD_RESET) {

            return response()->json([

                'message' => 'Password changed successfully',

            ]);

        }

        return response()->json([

            'message' => 'Invalid token',

        ], 400);

    }
}
