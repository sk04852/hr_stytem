<?php
namespace App\Http\Services;

use App\Http\Controllers\Expenses\Models\Expense;
use Carbon\Carbon;
use App\Http\Controllers\Invoices\Models\Invoice;
use App\Http\Controllers\Payments\Models\Payment;

class StatisticsService {
    private $invoiceModel_;
    private $paymentModel_;
    private $expenseModel_;
    public function __construct(
                                Invoice $invoiceModel,
                                Expense $expenseModel,
                                Payment $paymentModel) {
        $this->invoiceModel_ = $invoiceModel;
        $this->paymentModel_ = $paymentModel;
        $this->expenseModel_ = $expenseModel;
    }

    public function getAppData(int $companyId) {
        $lastMonth = new Carbon('first day of last month');
        $invoices = [];
        $payments = [];
        $expenses = [];
        $data = [];
            $invoices['this_month'] = (float) $this->invoiceModel_->where('company_id', $companyId)->whereMonth('invoice_date', now()->month)->sum('total');
            $invoices['last_month'] = (float) $this->invoiceModel_->where('company_id', $companyId)->whereMonth('invoice_date', $lastMonth->month)->sum('total');
        $data['sales'] = $invoices;
            $payments['this_month'] = (float) $this->paymentModel_->where('company_id', $companyId)->whereMonth('payment_date', now()->month)->sum('amount');
            $payments['last_month'] = (float) $this->paymentModel_->where('company_id', $companyId)->whereMonth('payment_date', $lastMonth->month)->sum('amount');
        $data['payments'] = $payments;
            $expenses['this_month'] = (float) $this->expenseModel_->where('company_id', $companyId)->whereMonth('date', now()->month)->sum('amount');
            $expenses['last_month'] = (float) $this->expenseModel_->where('company_id', $companyId)->whereMonth('date', $lastMonth->month)->sum('amount');
        $data['expenses'] = $expenses;

        return $data;
    }
}

?>
