<?php

namespace App\Http\Resources\Invoices\DriverInvoices;

use App\Http\Controllers\Transactions\Models\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverInvoicesCollection extends JsonResource
{
    public function getTransaction($invoiceId){
        return Transaction::select(['transaction_type_id', 'credit', 'debit'])->where('transaction_id', $invoiceId)->first();
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'invoice_no' => $this->invoice_id,
            'driver_id' => $this->driver_id,
            'status' => $this->status,
            'payment_type' => $this->getTransaction($this->invoice_id)->transaction_type_id == 2 ? 'Credit' : 'Debit',
            'paid_amount' => $this->getTransaction($this->invoice_id)->transaction_type_id == 2 ? $this->getTransaction($this->invoice_id)->credit : $this->getTransaction($this->invoice_id)->debit,
            'created_at' => $this->created_at,

        ];
    }
}
