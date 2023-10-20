<?php
namespace App\Http\Services;

use App\Http\Services\ReportsService;
use App\Http\Builders\CategoriesReportQueryBuilder;
use App\Http\Controllers\Vendors\Models\VendorCategory;

class CategoriesReportService {

    private $reportsService_;

    public function __construct(ReportsService $reportsService) {
        $this->reportsService_ = $reportsService;
    }

    public function categoriesReport($request){
        $downloadFileName = 'categories-report';
        $categoriesReportQueryBuilder = new CategoriesReportQueryBuilder(new VendorCategory(), $request);
        $reportQuery = $categoriesReportQueryBuilder->build();
        $columns = [
                    'code',
                    'name',
                    'company_name' => function($data){
                        return !empty($data['company_name']) ? $data['company_name'] : '--';
                    },
                    'total_products',
                    'created_at'
                   ];
        $title = 'Categories Report';
        $meta = [
            'Registered on' => 'Registered On Test',
            'Sort By' => 'Sort By Test'
        ];
        $type = $request->get('type', 'json');
        return $this->reportsService_->generateResponse($type, $reportQuery, $title, $meta, $columns, $downloadFileName);
    }


}

?>
