<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Telehealth\StoreTelehealthSessionRequest;
use App\Http\Requests\Api\Telehealth\UpdateTelehealthSessionRequest;
use App\Http\Resources\TelehealthSessionResource;
use App\Services\TelehealthSessionService;

class TelehealthSessionController extends Controller
{
    protected TelehealthSessionService $service;

    public function __construct(TelehealthSessionService $service)
    {
        $this->service = $service;
    }

    /**
     * List telehealth sessions for the authenticated user (doctor or patient).
     */
    public function mySessions()
    {
        $user = auth()->user();

        $query = \App\Models\TelehealthSession::with([
            'appointment.patient',
            'appointment.doctor.user',
            'appointment.hospital',
        ])->latest();

        if ($user->hasRole('doctor')) {
            $query->whereHas('appointment', function ($q) use ($user) {
                $q->where('doctor_id', $user->id);
            });
        } elseif ($user->hasRole('patient')) {
            $query->whereHas('appointment', function ($q) use ($user) {
                $q->where('patient_id', $user->id);
            });
        } elseif ($user->hasRole('hospital_admin')) {
            $hospitalIds = $user->hospitals()->pluck('hospitals.id');
            $query->whereHas('appointment', function ($q) use ($hospitalIds) {
                $q->whereIn('hospital_id', $hospitalIds);
            });
        }
        // platform_admin sees all

        return TelehealthSessionResource::collection($query->get());
    }

    /**
     * Get the telehealth session for a specific appointment.
     */
    public function byAppointment(string $appointmentId)
    {
        $session = \App\Models\TelehealthSession::with([
            'appointment.patient',
            'appointment.doctor.user',
            'appointment.hospital',
        ])->where('appointment_id', $appointmentId)->firstOrFail();

        return new TelehealthSessionResource($session);
    }
    // public function store(StoreTelehealthSessionRequest $request)
    // {
    //     $session = $this->service->createSession($request->validated());

    //     return new TelehealthSessionResource($session);
    // }
    public function show(string $id)
    {
        $session = $this->service->findSession($id);

        return new TelehealthSessionResource($session);
    }
    public function update(UpdateTelehealthSessionRequest $request, string $id)
    {
        $session = $this->service->updateSession($id, $request->validated());

        return new TelehealthSessionResource($session);
    }


    public function start(string $id)
    {
        $session = $this->service->startSession($id);

        return new TelehealthSessionResource($session);
    }

 
    public function complete(string $id)
    {
        $session = $this->service->completeSession($id);

        return new TelehealthSessionResource($session);
    }

    public function cancel(string $id)
    {
        $session = $this->service->cancelSession($id);

        return new TelehealthSessionResource($session);
    }

    /**
     * Reschedule a telehealth session by adding N minutes to the appointment time.
     * Body: { add_minutes: 10 }
     */
    public function reschedule(\Illuminate\Http\Request $request, string $id)
    {
        $data       = $request->validate(['add_minutes' => 'required|integer|min:1|max:120']);
        $session    = $this->service->rescheduleSession($id, (int) $data['add_minutes']);
        return new TelehealthSessionResource($session);
    }
    public function storeGoogleMeet(StoreTelehealthSessionRequest $request)
{
    $session = $this->service->createGoogleMeetSession($request->validated());

    return new TelehealthSessionResource($session);
}
public function store(StoreTelehealthSessionRequest $request)
{
    $session = $this->service->createSessionWithPlatform($request->validated());
    return new TelehealthSessionResource($session);
}

}
