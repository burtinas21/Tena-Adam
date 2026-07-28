<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payment\CreatePaymentRequest;
use App\Http\Requests\Api\Payment\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $this->authorize('viewAny', Payment::class);

        $payments = Payment::with([
            'appointment',
            'patient',
            'hospital',
            'invoice',
        ])->latest()->paginate(15);

        return PaymentResource::collection($payments);
    }

    /**
     * Store a newly created payment.
     */
    public function store(CreatePaymentRequest $request)
    {
        $this->authorize('create', Payment::class);

        $payment = $this->paymentService->create(
            $request->validated()
        );

        return new PaymentResource(
            $payment->load([
                'appointment',
                'patient',
                'hospital',
            ])
        );
    }

    /**
     * Display a payment.
     */
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        return new PaymentResource(
            $payment->load([
                'appointment',
                'patient',
                'hospital',
                'invoice',
            ])
        );
    }

    /**
     * Update payment.
     */
    public function update(
        UpdatePaymentRequest $request,
        Payment $payment
    ) {

        $this->authorize('update', $payment);

        $payment = $this->paymentService->update(
            $payment,
            $request->validated()
        );

        return new PaymentResource($payment);
    }

    /**
     * Delete payment.
     */
    public function destroy(Payment $payment)
    {
        $this->authorize('delete', $payment);

        $this->paymentService->delete($payment);

        return response()->json([
            'message' => 'Payment deleted successfully.'
        ]);
    }
}