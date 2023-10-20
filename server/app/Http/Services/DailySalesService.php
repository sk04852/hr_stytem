<?php
namespace App\Http\Services;

use App\Http\Builders\DailySalesReportQueryBuilder;
use App\Http\Controllers\Invoices\Models\Invoice;
use App\Http\Services\ReportsService;

class DailySalesService {

    private $reportsService_;

    public function __construct(ReportsService $reportsService) {
        $this->reportsService_ = $reportsService;
    }

    public function dailySalesReport($request){
        $downloadFileName = 'daily-sales-report';
        $dailySalesReportQueryBuilder = new DailySalesReportQueryBuilder (new Invoice(), $request);
        $reportQuery = $dailySalesReportQueryBuilder->build();
        $columns = [
                  
                    'company',
                    'total_invoices',
                    'total',
                 
                   ];
        $title = 'Daily Sales Report';
        $meta = [
           
        ];
        $type = $request->get('type', 'json');
        return $this->reportsService_->generateResponse($type, $reportQuery, $title, $meta, $columns, $downloadFileName);
    }


}

?>
