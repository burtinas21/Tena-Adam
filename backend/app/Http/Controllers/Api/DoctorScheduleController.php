<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use App\Http\Requests\Api\Doctor\StoreDoctorScheduleRequest;
use App\Http\Requests\Api\Doctor\UpdateDoctorScheduleRequest;
use App\Services\DoctorScheduleService;

class DoctorScheduleController extends Controller
{
    public function __construct(
        private DoctorScheduleService $service
    ) {}

    public function index()
    {
        $this->authorize('viewAny', DoctorSchedule::class);

        $user  = auth()->user();
        $query = DoctorSchedule::with('doctor.user');

        // Scope receptionist to their hospital's doctors only
        if ($user->hasRole('receptionist')) {
            $hospitalId = $user->hospitalStaff()->value('hospital_id');
            if ($hospitalId) {
                $query->whereHas('doctor', fn($q) => $q->where('hospital_id', $hospitalId));
            }
        } elseif ($user->hasRole('doctor')) {
            $query->where('doctor_id', $user->id);
        }
        // hospital_admin and platform_admin see all

        return response()->json(['data' => $query->get()]);
    }

    public function store(StoreDoctorScheduleRequest $request)
    {
        $this->authorize('create', DoctorSchedule::class);

        $schedule = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Schedule created successfully',
            'data' => $schedule
        ], 201);
    }

    public function update(
    UpdateDoctorScheduleRequest $request,
    DoctorSchedule $doctorSchedule
) {
    $this->authorize('update', $doctorSchedule);

    $schedule = $this->service->update(
        $doctorSchedule,
        $request->validated()
    );

    return response()->json([
        'message' => 'Schedule updated successfully',
        'data' => $schedule->fresh(['doctor.user'])
    ]);
}

    public function destroy(DoctorSchedule $doctorSchedule)
    {
        $this->authorize('delete', $doctorSchedule);

        $this->service->delete($doctorSchedule);

        return response()->json([
            'message' => 'Schedule deleted successfully'
        ]);
    }

    public function show(DoctorSchedule $doctorSchedule)
{
    $this->authorize('view', $doctorSchedule);

    return response()->json([
        'data' => $doctorSchedule->load('doctor.user')
    ]);
}
}