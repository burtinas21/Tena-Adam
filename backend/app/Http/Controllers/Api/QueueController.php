<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AppointmentToQueueService;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function __construct(
        private QueueService $service,
        private AppointmentToQueueService $appointmentToQueueService,
    ) {}

    // -----------------------------------------------------------------------
    // GET /queue/doctor/{doctorId}?date=YYYY-MM-DD
    // -----------------------------------------------------------------------

    public function doctorQueue(string $doctorId, Request $request): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());

        $queue = $this->service->getDoctorQueue($doctorId, $date);

        return response()->json(['data' => $queue]);
    }

    // -----------------------------------------------------------------------
    // POST /queue/init
    // Generates queue entries from today's appointments for a doctor.
    // Safe to call multiple times — skips if queue already exists.
    // Body: { doctor_id, date }
    // -----------------------------------------------------------------------

    public function init(Request $request): JsonResponse
    {
        $data = $request->validate([
            'doctor_id' => 'required|uuid|exists:healthcare_providers,id',
            'date'      => 'required|date',
        ]);

        $result = $this->appointmentToQueueService->generate(
            $data['doctor_id'],
            $data['date']
        );

        // After generating from appointments, return the full queue list
        $queue = $this->service->getDoctorQueue($data['doctor_id'], $data['date']);

        return response()->json([
            'message' => $result['message'],
            'total'   => $result['total'] ?? 0,
            'data'    => $queue,
        ]);
    }

    // -----------------------------------------------------------------------
    // POST /queue/generate
    // Body: { doctor_id, hospital_id, queue_date,
    //         appointment_id?, walk_in_patient_name?, walk_in_phone? }
    // -----------------------------------------------------------------------

    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'doctor_id'            => 'required|uuid|exists:healthcare_providers,id',
            'hospital_id'          => 'required|uuid|exists:hospitals,id',
            'queue_date'           => 'required|date',
            'appointment_id'       => 'nullable|uuid|exists:appointments,id',
            'walk_in_patient_name' => 'nullable|string|max:255',
            'walk_in_phone'        => 'nullable|string|max:20',
        ]);

        $entry = $this->service->generate($data);

        return response()->json([
            'message' => 'Queue entry created',
            'data'    => $entry,
        ], 201);
    }

    // -----------------------------------------------------------------------
    // POST /queue/call-next
    // Body: { doctor_id, date }
    // -----------------------------------------------------------------------

    public function callNext(Request $request): JsonResponse
    {
        $data = $request->validate([
            'doctor_id' => 'required|uuid|exists:healthcare_providers,id',
            'date'      => 'required|date',
        ]);

        $result = $this->service->callNextPatient(
            $data['doctor_id'],
            $data['date']
        );

        return response()->json($result);
    }

    // -----------------------------------------------------------------------
    // POST /queue/complete
    // Body: { queue_id }
    // -----------------------------------------------------------------------

    public function complete(Request $request): JsonResponse
    {
        $data = $request->validate([
            'queue_id' => 'required|uuid|exists:queue,id',
        ]);

        $result = $this->service->completeConsultation($data['queue_id']);

        return response()->json($result);
    }

    // -----------------------------------------------------------------------
    // POST /queue/skip
    // Body: { queue_id }
    // -----------------------------------------------------------------------

    public function skip(Request $request): JsonResponse
    {
        $data = $request->validate([
            'queue_id' => 'required|uuid|exists:queue,id',
        ]);

        try {
            $result = $this->service->skipPatient($data['queue_id']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($result);
    }

    // -----------------------------------------------------------------------
    // POST /queue/recall
    // Body: { queue_id }
    // -----------------------------------------------------------------------

    public function recall(Request $request): JsonResponse
    {
        $data = $request->validate([
            'queue_id' => 'required|uuid|exists:queue,id',
        ]);

        $result = $this->service->recallPatient($data['queue_id']);

        return response()->json($result);
    }
}
