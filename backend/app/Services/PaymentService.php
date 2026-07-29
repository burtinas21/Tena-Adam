<?php

namespace App\Services;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Invoice;
use App\Models\Appointment;
use Carbon\Carbon;

class PaymentService
{
    public function __construct(
    private ChapaService $chapaService,
    private InvoiceService $invoiceService
) {
}

    /**
     * Create a payment and initialize Chapa payment.
     */
    public function create(array $data): array
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Generate Internal Reference
            |--------------------------------------------------------------------------
            */

            $reference = 'PAY-' . strtoupper(Str::random(12));

            /*
            |--------------------------------------------------------------------------
            | Create Payment Record
            |--------------------------------------------------------------------------
            */

            $payment = Payment::create([

                'appointment_id'    => $data['appointment_id'] ?? null,

                'patient_id'        => $data['patient_id'],

                'hospital_id'       => $data['hospital_id'],

                'payment_method_id' => $data['payment_method_id'],

                'amount'            => $data['amount'],

                'currency'          => $data['currency'] ?? 'ETB',

                'status'            => 'pending',

                'reference'         => $reference,

                'payment_date'      => null,

                'metadata'          => $data['metadata'] ?? null,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Initialize Chapa
            |--------------------------------------------------------------------------
            */

            $response = $this->chapaService->initializePayment([

                'amount'      => $payment->amount,

                'currency'    => $payment->currency,

                'email'       => $data['email'],

                'first_name'  => $data['first_name'],

                'last_name'   => $data['last_name'],

                'tx_ref'      => $reference,

                'callback_url' => route('payments.callback'),

                // Include tx_ref in return_url so the success page can verify the payment
                // even in local/dev where Chapa cannot reach our callback endpoint.
                'return_url'  => config('app.frontend_url') . '/patient/paymentSuccess?tx_ref=' . $reference,

                'customization' => [

                    'title'       => 'Tena Adam',

                    'description' => 'Appointment Payment',

                ],

            ]);

            /*
            |--------------------------------------------------------------------------
            | Save Transaction Reference
            |--------------------------------------------------------------------------
            | Chapa's initialize response only returns checkout_url in data[].
            | The tx_ref we sent is our own reference, so we store it directly.
            |--------------------------------------------------------------------------
            */

            $payment->update([

                'transaction_id' => $reference,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Return Payment + Checkout URL
            |--------------------------------------------------------------------------
            */

            return [

                'payment' => $payment->fresh(),

                'checkout_url' => $response['data']['checkout_url'],

            ];

        });
    }

    /**
     * Mark payment completed.
     */
    public function complete(Payment $payment): Payment
    {
        $payment->update([

            'status' => 'completed',

            'payment_date' => now(),

        ]);

        return $payment->fresh();
    }

    /**
     * Mark payment failed.
     */
    public function fail(Payment $payment): Payment
    {
        $payment->update([

            'status' => 'failed',

        ]);

        return $payment->fresh();
    }

    /**
     * Cancel payment.
     */
    public function cancel(Payment $payment): Payment
    {
        $payment->update([

            'status' => 'cancelled',

        ]);

        return $payment->fresh();
    }

    /**
     * Update payment.
     */
    public function update(Payment $payment, array $data): Payment
    {
        $payment->update($data);

        return $payment->fresh();
    }

    /**
     * Re-initialize a pending payment with Chapa and return a fresh checkout URL.
     * Called when the patient clicks "Pay Now" on a pending_payment appointment.
     *
     * Chapa rejects reuse of a tx_ref that was already submitted, so we generate
     * a brand-new reference and update the payment record accordingly.
     */
    public function reinitialize(Payment $payment): string
    {
        $payment->load(['appointment.patient.user', 'appointment.doctor.user']);

        $patient = $payment->appointment?->patient?->user ?? null;

        // Generate a fresh reference so Chapa doesn't reject "used before"
        $newReference = 'PAY-' . strtoupper(Str::random(12));

        $response = $this->chapaService->initializePayment([
            'amount'       => $payment->amount,
            'currency'     => $payment->currency,
            'email'        => $patient?->email ?? '',
            'first_name'   => $patient?->first_name ?? '',
            'last_name'    => $patient?->last_name ?? '',
            'tx_ref'       => $newReference,
            'callback_url' => route('payments.callback'),
            // Include tx_ref in return_url so the success page can verify the payment
            'return_url'   => config('app.frontend_url') . '/patient/paymentSuccess?tx_ref=' . $newReference,
            'customization' => [
                'title'       => 'Tena Adam',
                'description' => 'Appointment Payment',
            ],
        ]);

        // Persist the new reference so the callback can match this payment
        $payment->update([
            'reference'      => $newReference,
            'transaction_id' => $newReference,
        ]);

        return $response['data']['checkout_url'];
    }

    /**
     * Delete payment.
     */
    public function delete(Payment $payment): bool
    {
        return $payment->delete();
    }
    /**
     * Confirm the appointment and generate its queue entry after a successful payment.
     * Called from both handleCallback and handleWebhook.
     */
    private function confirmAppointmentAndGenerateQueue(Payment $payment): void
    {
        if (! $payment->appointment_id) {
            return;
        }

        $appointment = Appointment::find($payment->appointment_id);

        if (! $appointment) {
            return;
        }

        // Confirm the appointment
        if ($appointment->status === 'pending_payment') {
            $appointment->update([
                'status'      => 'confirmed',
                'approved_at' => now(),
            ]);
        }

        // Generate queue entry for this appointment
        try {
            app(\App\Services\AppointmentToQueueService::class)
                ->generate(
                    $appointment->doctor_id,
                    Carbon::parse($appointment->scheduled_time)->toDateString()
                );
        } catch (\Throwable) {
            // Queue generation is non-blocking — never fail the payment callback
        }
    }

    /**
 * Handle Chapa callback.
 */
public function handleCallback(array $callbackData): array
{
    /*
    |--------------------------------------------------------------------------
    | Get Transaction Reference
    |--------------------------------------------------------------------------
    */

    $txRef = $callbackData['tx_ref'] ?? null;

    if (!$txRef) {

        return [

            'success' => false,

            'message' => 'Transaction reference is missing.'

        ];

    }

    /*
    |--------------------------------------------------------------------------
    | Find Payment
    |--------------------------------------------------------------------------
    */

    $payment = Payment::where(
        'reference',
        $txRef
    )->first();

    if (!$payment) {

        return [

            'success' => false,

            'message' => 'Payment not found.'

        ];

    }

    /*
    |--------------------------------------------------------------------------
    | Verify Payment With Chapa
    |--------------------------------------------------------------------------
    */

    $verification = $this->chapaService
        ->verifyPayment($txRef);

    /*
    |--------------------------------------------------------------------------
    | Payment Successful
    |--------------------------------------------------------------------------
    */

    if (

        isset($verification['status'])

        &&

        $verification['status'] === 'success'

    ) {

        $payment->update([

            'status' => 'completed',

            'payment_date' => now(),

            'transaction_id' =>

                $verification['data']['id'] ?? null,

            'metadata' => $verification,

        ]);

        $this->invoiceService->createFromPayment(
    $payment
);

        $this->confirmAppointmentAndGenerateQueue($payment);

        // Reload to get the invoice
        $payment->load('invoice');

        return [

            'success' => true,

            'message' => 'Payment verified successfully.',

            'invoice_id' => $payment->invoice?->id,

        ];

    }

    /*
    |--------------------------------------------------------------------------
    | Payment Failed
    |--------------------------------------------------------------------------
    */

    $payment->update([

        'status' => 'failed',

        'metadata' => $verification,

    ]);

    return [

        'success' => false,

        'message' => 'Payment verification failed.'

    ];
}
public function handleWebhook(array $payload): array
{
    /*
    |--------------------------------------------------------------------------
    | Get Transaction Reference
    |--------------------------------------------------------------------------
    */

    $txRef = $payload['tx_ref'] ?? null;

    if (!$txRef) {

        return [

            'success' => false,

            'message' => 'Transaction reference is missing.'

        ];

    }

    /*
    |--------------------------------------------------------------------------
    | Find Payment
    |--------------------------------------------------------------------------
    */

    $payment = Payment::where(
        'reference',
        $txRef
    )->first();

    if (!$payment) {

        return [

            'success' => false,

            'message' => 'Payment not found.'

        ];

    }

    /*
    |--------------------------------------------------------------------------
    | Already Completed
    |--------------------------------------------------------------------------
    */

    if ($payment->status === 'completed') {

        return [

            'success' => true,

            'message' => 'Payment already processed.'

        ];

    }

    /*
    |--------------------------------------------------------------------------
    | Verify With Chapa
    |--------------------------------------------------------------------------
    */

    $verification = $this->chapaService
        ->verifyPayment($txRef);

    if (
        isset($verification['status']) &&
        $verification['status'] === 'success'
    ) {

        $payment->update([

            'status' => 'completed',

            'payment_date' => now(),

            'transaction_id' =>
                $verification['data']['id'] ?? null,

            'metadata' => $verification,

        ]);

        $this->invoiceService
            ->createFromPayment($payment);

        $this->confirmAppointmentAndGenerateQueue($payment);

        return [

            'success' => true,

            'message' => 'Webhook processed successfully.'

        ];

    }

    $payment->update([

        'status' => 'failed',

        'metadata' => $verification,

    ]);

    return [

        'success' => false,

        'message' => 'Payment verification failed.'

    ];
}
}