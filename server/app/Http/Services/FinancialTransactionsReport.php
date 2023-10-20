<?php

namespace App\Http\Services;

use App\Http\Builders\FinancialTransactionsQueryBuilder;
use App\Http\Services\ReportsService;
use App\Http\Controllers\MonetaryTransactions\Models\MonetaryTransaction;

class FinancialTransactionsReport
{

    private $reportsService_;

    public function __construct(ReportsService $reportsService)
    {
        $this->reportsService_ = $reportsService;
    }

    public function tradingPlateFormReport($request)
    {
        $downloadFileName = 'Transaction Report';
        $tradingPlateFormAccountReportQueryBuilder = new FinancialTransactionsQueryBuilder(new MonetaryTransaction(), $request);
        $reportQuery = $tradingPlateFormAccountReportQueryBuilder->build();
        $columns = [
            'account_id',
            'account_name',
            'amount',
            'transaction_type',
            'date',
            'crm_comment',
            'assigned_to',
            'created_at'
        ];
        $title = 'Transactions Report';
        $meta = [];
        $type = $request->get('type', 'json');
        return $this->reportsService_->generateResponse($type, $reportQuery, $title, $meta, $columns, $downloadFileName);
    }
}
