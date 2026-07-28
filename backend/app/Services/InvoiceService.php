<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\Invoice;

class PaymentService
{
    /**
     * Create a payment.
     */
    public function create(array $data): Payment
    {
        return DB::transaction(function () use ($data) {

            return Payment::create([

                'appointment_id'   => $data['appointment_id'] ?? null,

                'patient_id'       => $data['patient_id'],

                'hospital_id'      => $data['hospital_id'],

                'payment_method_id'=> $data['payment_method_id'],

                'amount'           => $data['amount'],

                'currency'         => $data['currency'] ?? 'ETB',

                'status'           => 'pending',

                'transaction_id'   => $data['transaction_id'] ?? null,

                'reference'        => Str::upper(Str::random(12)),

                'payment_date'     => null,

                'metadata'         => $data['metadata'] ?? null,

            ]);

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
    public function createFromPayment(Payment $payment): Invoice
{
    $invoice = Invoice::firstOrCreate(

        [
            'payment_id' => $payment->id,
        ],

        [
            'invoice_number' => $this->generateInvoiceNumber(),

            'due_date' => now(),

            'status' => 'paid',
        ]

    );

    $this->generatePdf($invoice);

    return $invoice;
}

    /**
     * Generate unique invoice number.
     */
    private function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('YmdHis');
    }
    public function generatePdf(Invoice $invoice): Invoice
{
    $invoice->load([

        'payment.patient.user',

        'payment.hospital',

        'payment.appointment',

    ]);

    $pdf = Pdf::loadView(

        'invoices.invoice',

        compact('invoice')

    );

    $fileName =

        $invoice->invoice_number . '.pdf';

    $path =

        'invoices/' . $fileName;

    Storage::disk('public')->put(

        $path,

        $pdf->output()

    );

    $invoice->update([

        'pdf_url' =>

            Storage::url($path)

    ]);

    return $invoice->fresh();
}
}