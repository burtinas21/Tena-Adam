<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\PasswordController;
use App\Http\Controllers\Api\AuthController;
Route::post('/forgot-password', [PasswordController::class, 'forgot']);

Route::post('/reset-password', [PasswordController::class, 'reset']);
Route::middleware([ 'auth:sanctum','permission:manage_users'])->group(function () {

    Route::get('/users', function () {});

});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register',[AuthController::class,'register']);