<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('payment')
            ->latest()
            ->paginate(15);

        return InvoiceResource::collection($invoices);
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        return new InvoiceResource(
            $invoice->load('payment')
        );
    }
public function download(Invoice $invoice)
{
    if (!$invoice->pdf_url) {

        abort(404, 'Invoice PDF not found.');

    }

    $path = str_replace(

        '/storage/',

        '',

        $invoice->pdf_url

    );

    return Storage::disk('public')->download($path);
}
}