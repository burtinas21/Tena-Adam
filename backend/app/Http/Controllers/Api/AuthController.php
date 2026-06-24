<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\Http\Requests\LoginRequest;

use App\Http\Requests\RegisterRequest;

use App\Services\AuthService;


class AuthController extends Controller
{


    protected AuthService $authService;



    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;

    }





    public function register(RegisterRequest $request)
    {


        $result = $this->authService->register(
            $request->validated()
        );



        return response()->json([


            'message'=>'Registration successful',


            'data'=>$result


        ],201);


    }





    public function login(LoginRequest $request)
    {


        $result = $this->authService->login(
            $request->validated()
        );



        if(!$result)
        {


            return response()->json([

                'message'=>'Invalid credentials'

            ],401);


        }



        return response()->json([


            'message'=>'Login successful',


            'data'=>$result


        ]);



    }



}