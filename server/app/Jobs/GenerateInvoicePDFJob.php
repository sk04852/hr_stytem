<?php

namespace App\Jobs;

use App\Events\InvoiceEmailEvent;
use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Invoices\Models\Invoice;
use App\Http\Controllers\Invoices\Models\InvoiceProduct;
use App\Http\Services\InvoicesService;
use App\Mail\InvoiceEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class GenerateInvoicePDFJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $invoiceId;
    private $invoiceService_;


    public function __construct($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    public function handle(InvoicesService $service)
    {
        $this->invoiceService_ = $service;
        $this->invoiceService_->createPDF($this->invoiceId);
    }
}
