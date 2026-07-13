<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserNotificationPreferenceRequest;
use App\Http\Requests\UpdateUserNotificationPreferenceRequest;
use App\Models\UserNotificationPreference;

class UserNotificationPreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserNotificationPreferenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UserNotificationPreference $userNotificationPreference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserNotificationPreference $userNotificationPreference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserNotificationPreferenceRequest $request, UserNotificationPreference $userNotificationPreference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserNotificationPreference $userNotificationPreference)
    {
        //
    }
}
