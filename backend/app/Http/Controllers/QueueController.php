<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Services\QueueService;
use App\Services\AppointmentToQueueService;

use App\Http\Requests\Queue\GenerateQueueRequest;
use App\Http\Requests\Queue\CallNextRequest;
use App\Http\Requests\Queue\SkipPatientRequest;
use App\Http\Requests\Queue\CompletePatientRequest;
use App\Http\Requests\Queue\RecallPatientRequest;

use App\Http\Resources\QueueResource;

use App\Models\Queue;

class QueueController extends Controller
{
    protected QueueService $queueService;
    protected AppointmentToQueueService $queueGenerator;

    public function __construct(
        QueueService $queueService,
        AppointmentToQueueService $queueGenerator
    ) {
        $this->queueService = $queueService;
        $this->queueGenerator = $queueGenerator;
    }
        public function callNext(CallNextRequest $request)
    {
        $this->authorize('callNext', Queue::class);

        $queue = $this->queueService->callNextPatient(
            $request->doctor_id,
            now()->toDateString()
        );

        return response()->json([
            'message' => 'Next patient called',
            'data' => $queue
        ]);
    }
        public function complete(CompletePatientRequest $request)
    {
        $queue = $this->queueService->completePatient(
            $request->queue_id
        );

        $this->authorize('complete', $queue);

        return new QueueResource($queue);
    }
        public function skip(SkipPatientRequest $request)
    {
        $queue = $this->queueService->skipPatient(
            $request->queue_id
        );

        $this->authorize('skip', $queue);

        return new QueueResource($queue);
    }
        public function recall(RecallPatientRequest $request)
    {
        $queue = $this->queueService->recallPatient(
            $request->queue_id
        );

        $this->authorize('recall', $queue);

        return new QueueResource($queue);
    }
        public function doctorQueue($doctorId)
    {
        $queues = Queue::where('doctor_id', $doctorId)
            ->whereDate('queue_date', now())
            ->orderBy('queue_number')
            ->get();

        return QueueResource::collection($queues);
    }
}