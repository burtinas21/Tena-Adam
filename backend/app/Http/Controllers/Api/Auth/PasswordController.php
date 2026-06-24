<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    public function forgot(ForgotPasswordRequest $request)
    {
        Password::sendResetLink(
            $request->only('email')
        );

        return response()->json([
            'message' => 'Reset link sent'
        ]);
    }


    public function reset(ResetPasswordRequest $request)
    {
        Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user, $password) {

                $user->password = Hash::make($password);

                $user->save();
            }
        );


        return response()->json([
            'message' => 'Password reset successful'
        ]);
    }
}