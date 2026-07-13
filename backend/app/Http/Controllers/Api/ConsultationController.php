<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConsultationResource;
use App\Models\Queue;
use App\Services\Consultation\ConsultationService;
use Illuminate\Http\JsonResponse;

class ConsultationController extends Controller
{
    public function __construct(
        private ConsultationService $consultationService
    ) {}
    public function show(Queue $queue): JsonResponse
    {
        $this->authorize('openConsultation', $queue);

        $consultation = $this->consultationService
            ->openConsultation($queue->id);

        return response()->json([
            'message' => 'Consultation loaded successfully.',
            'data' => new ConsultationResource($consultation),
        ]);
    }
}