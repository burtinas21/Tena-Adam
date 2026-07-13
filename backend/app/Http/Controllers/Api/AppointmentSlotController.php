<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppointmentSlot;
use App\Models\HealthcareProvider;
use App\Services\AppointmentSlotService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentSlotController extends Controller
{
    public function __construct(
        private AppointmentSlotService $service
    ) {}

 
    public function generate(Request $request)
    {
        $request->validate([
            'doctor_id' => ['required', 'uuid'],
            'date' => ['required', 'date']
        ]);

        $doctor = HealthcareProvider::findOrFail($request->doctor_id);

        $slots = $this->service->generateSlots(
            $doctor,
            Carbon::parse($request->date)
        );

        return response()->json([
            'message' => 'Slots generated successfully',
            'data' => $slots
        ]);
    }

    public function block(Request $request)
    {
        $request->validate([
            'doctor_id' => ['required', 'uuid'],
            'date' => ['required', 'date']
        ]);

        $doctor = HealthcareProvider::findOrFail($request->doctor_id);

        $this->service->blockSlotsForLeave(
            $doctor,
            Carbon::parse($request->date)
        );

        return response()->json([
            'message' => 'Slots blocked successfully'
        ]);
    }

    public function book(AppointmentSlot $slot)
    {
        $slot = $this->service->bookSlot($slot);

        return response()->json([
            'message' => 'Slot booked successfully',
            'data' => $slot
        ]);
    }

    public function release(AppointmentSlot $slot)
    {
        $slot = $this->service->releaseSlot($slot);

        return response()->json([
            'message' => 'Slot released successfully',
            'data' => $slot
        ]);
    }

  
    public function complete(AppointmentSlot $slot)
    {
        $slot = $this->service->completeSlot($slot);

        return response()->json([
            'message' => 'Slot completed successfully',
            'data' => $slot
        ]);
    }
}