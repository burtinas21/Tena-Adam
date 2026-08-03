<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Doctor queue channel — used by the doctor's MyQueue view and Medical Encounters page
Broadcast::channel('queue.{doctorId}', function ($user, $doctorId) {
    return true; // restrict by role in production
});

// Hospital-wide queue channel — used by receptionist and admin views
Broadcast::channel('reception.queue', function ($user) {
    return true; // restrict by hospital membership in production
});
