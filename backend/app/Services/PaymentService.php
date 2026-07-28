<?php

namespace App\Services;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Invoice;
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

                'return_url'  => config('app.frontend_url') . '/payment/success',

                'customization' => [

                    'title'       => 'Tena Adam Healthcare',

                    'description' => 'Appointment Payment',

                ],

            ]);

            /*
            |--------------------------------------------------------------------------
            | Save Transaction Reference
            |--------------------------------------------------------------------------
            */

            $payment->update([

                'transaction_id' => $response['data']['tx_ref'],

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
     * Delete payment.
     */
    public function delete(Payment $payment): bool
    {
        return $payment->delete();
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

        return [

            'success' => true,

            'message' => 'Payment verified successfully.'

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