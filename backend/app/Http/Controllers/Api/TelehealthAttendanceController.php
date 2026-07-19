<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Telehealth\StoreTelehealthAttendanceRequest;
use App\Http\Requests\Api\Telehealth\UpdateTelehealthAttendanceRequest;
use App\Http\Resources\TelehealthAttendanceResource;
use App\Services\TelehealthAttendanceService;

class TelehealthAttendanceController extends Controller
{
    protected TelehealthAttendanceService $service;

    public function __construct(TelehealthAttendanceService $service)
    {
        $this->service = $service;
    }

    /**
     * Join a telehealth session.
     */
    public function store(StoreTelehealthAttendanceRequest $request)
    {
        $attendance = $this->service->joinAttendance($request->validated());

        return new TelehealthAttendanceResource($attendance);
    }

    /**
     * Leave a telehealth session.
     */
    public function update(UpdateTelehealthAttendanceRequest $request, string $sessionId, string $userId)
    {
        $attendance = $this->service->leaveAttendance($sessionId, $userId);

        return new TelehealthAttendanceResource($attendance);
    }

    /**
     * List all attendance records for a session.
     */
    public function index(string $sessionId)
    {
        $attendance = $this->service->listAttendance($sessionId);

        return TelehealthAttendanceResource::collection($attendance);
    }
}
