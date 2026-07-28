<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRefundRequest;
use App\Http\Resources\RefundResource;
use App\Models\Refund;
use App\Services\RefundService;

class RefundController extends Controller
{
    public function __construct(
        private RefundService $refundService
    ) {}

    /**
     * List refunds.
     */
    public function index()
    {
        $refunds = Refund::with('payment')
            ->latest()
            ->paginate(15);

        return RefundResource::collection($refunds);
    }

    /**
     * Create refund request.
     */
    public function store(StoreRefundRequest $request)
    {
        $this->authorize('create', Refund::class);

        $refund = $this->refundService->create(
            $request->validated()
        );

        return new RefundResource(
            $refund->load('payment')
        );
    }

    /**
     * Show refund.
     */
    public function show(Refund $refund)
    {
        $this->authorize('view', $refund);

        return new RefundResource(
            $refund->load('payment')
        );
    }

    /**
     * Approve refund.
     */
    public function approve(Refund $refund)
    {
        $this->authorize('approve', $refund);

        $refund = $this->refundService->approve(
            $refund,
            auth()->id()
        );

        return new RefundResource($refund);
    }

    /**
     * Process refund.
     */
    public function process(Refund $refund)
    {
        $this->authorize('approve', $refund);

        $refund = $this->refundService->process($refund);

        return new RefundResource($refund);
    }
}