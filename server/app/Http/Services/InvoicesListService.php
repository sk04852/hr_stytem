<?php
namespace App\Http\Services;

use App\Http\Builders\InvoicesListReportQueryBuilder;
use App\Http\Controllers\Invoices\Models\Invoice;
use App\Http\Services\ReportsService;

class InvoicesListService {

    private $reportsService_;

    public function __construct(ReportsService $reportsService) {
        $this->reportsService_ = $reportsService;
    }

    public function invoicesListReport($request){
        $downloadFileName = 'invoices-list-report';
        $invoicesListReportQueryBuilder = new InvoicesListReportQueryBuilder(new Invoice(), $request);
        $reportQuery = $invoicesListReportQueryBuilder->build();
        $columns = [
                    'invoice_id',
                    'invoice_date',
                    'invoice_number',
                    'payment_type',
                    'tax',
                    'total',
                    'customer_id',
                    'customer',
                    'created_at'
                   ];
        $title = 'Invoices List Report';
        $meta = [
            
        ];
        $type = $request->get('type', 'json');
        return $this->reportsService_->generateResponse($type, $reportQuery, $title, $meta, $columns, $downloadFileName);
    }


}

?>
