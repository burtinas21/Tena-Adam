<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payment\StorePaymentRequest;
use App\Http\Requests\Api\Payment\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
    public function store(StorePaymentRequest $request)
    {
        $this->authorize('create', Payment::class);
$result = $this->paymentService->create(
    $request->validated()
);

return response()->json([

    'message' => 'Payment initialized successfully.',

    'payment' => new PaymentResource(
        $result['payment']->load([
            'appointment',
            'patient',
            'hospital',
        ])
    ),

    'checkout_url' => $result['checkout_url'],

]);
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
public function callback(Request $request): JsonResponse
{
    $result = $this->paymentService->handleCallback(
        $request->all()
    );

    return response()->json($result);
}

public function webhook(Request $request): JsonResponse
{
    $result = $this->paymentService->handleWebhook(
        $request->all()
    );

    return response()->json($result);
}

/**
 * Find the pending payment for a given appointment (patient-facing).
 */
public function byAppointment(Request $request): JsonResponse
{
    $request->validate([
        'appointment_id' => ['required', 'uuid', 'exists:appointments,id'],
    ]);

    $payment = Payment::with(['invoice'])
        ->where('appointment_id', $request->appointment_id)
        ->where('status', 'pending')
        ->latest()
        ->first();

    if (! $payment) {
        return response()->json(['message' => 'No pending payment found for this appointment.'], 404);
    }

    return response()->json(['data' => new PaymentResource($payment)]);
}

/**
 * Re-initialize a pending Chapa payment and return a fresh checkout URL.
 * Used by the "Pay Now" button when the original checkout URL was not stored.
 */
public function reinitialize(Payment $payment): JsonResponse
{
    // Only the owning patient or admin may do this
    $this->authorize('view', $payment);

    if ($payment->status !== 'pending') {
        return response()->json(['message' => 'Payment is not in a pending state.'], 422);
    }

    $checkoutUrl = $this->paymentService->reinitialize($payment);

    return response()->json([
        'checkout_url' => $checkoutUrl,
    ]);
}
}