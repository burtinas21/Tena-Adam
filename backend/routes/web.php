<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-mail', function () {

    Mail::raw('Congratulations! Laravel SMTP is working correctly.', function ($message) {

        $message->to('georgehabte21@gmail.com')
                ->subject('Laravel SMTP Test');

    });

    return 'Email sent successfully!';
});