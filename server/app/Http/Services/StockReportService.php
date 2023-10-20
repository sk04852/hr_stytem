<?php
namespace App\Http\Services;

use App\Http\Builders\StockReportQueryBuilder;
use App\Http\Controllers\VendorProducts\Models\VendorProduct;
use App\Http\Services\ReportsService;
use Illuminate\Http\Request;

class StockReportService {

    private $reportsService_;

    public function __construct(ReportsService $reportsService) {
        $this->reportsService_ = $reportsService;
    }
    public function getVendorProductsStockReport(Request $request, int $companyId) {
        $downloadFileName = 'Stock Report( '.now().' )';
        $stockReportQueryBuilder = new StockReportQueryBuilder(new VendorProduct());

        $stockReportQueryBuilder->setCompanyId((int) $companyId);
        if($request->has('client_id')) {
            $stockReportQueryBuilder->setClientId($request->get('client_id'));
        }
        if($request->has('products_ids')) {
            $stockReportQueryBuilder->setProductIds((array) $request->get('products_ids'));
        }
        if($request->has('form')) {
            $stockReportQueryBuilder->setFromDatetime($request->get('from'));
        }
        if($request->has('to')) {
            $stockReportQueryBuilder->setToDatetime($request->get('to'));
        }
        
        $availableQuantity = function($record){
                                return $this->getAvailableQuantity($record);
                            };
        $sellingValue = function($record){
                        return $record['unit_price']*($this->getAvailableQuantity($record));
                    };
        $costValue = function($record){
                        return ( $record['purchase_price']*($this->getAvailableQuantity($record)));
                    };
        $reportQuery = $stockReportQueryBuilder->build();
        $columns = [
                    'Code',
                    'Name',
                    'Unit Price',
                    'Purchase Price',
                    'Available Quantity' => $availableQuantity,
                    'Selling Value' => $sellingValue,
                    'Cost Value' => $costValue,
                    ];
        $title = 'Stock Report';
        $meta = [
            'Registered on' => 'Registered On Test',
            'Sort By' => 'Sort By Test'
        ];
        $type = $request->get('type', 'json');
        return $this->reportsService_->generateResponse($type, $reportQuery, $title, $meta, $columns, $downloadFileName);
    }
    public function getAvailableQuantity($record){
        return ( $record['available_quantity'] - $record['expired'] )- $record['damaged'];
    }
}

?>
