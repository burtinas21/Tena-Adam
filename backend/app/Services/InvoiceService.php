<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\Invoice;

class InvoiceService
{
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