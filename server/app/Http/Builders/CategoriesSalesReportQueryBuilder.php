<?php
namespace App\Http\Builders;

use App\Http\Builders\Builder;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class CategoriesSalesReportQueryBuilder implements Builder {

    private $queryBuilder_;
    private $productIds_ = [];
    private $companyId_ = -1;
    private $fromDatetime_ = null;
    private $toDatetime_ = null;

    public function __construct(BaseModel $queryBuilder) {
        $this->queryBuilder_ = $queryBuilder;
    }

    public function setProductIds(array $productIds) {
        $this->productIds_ = $productIds;
    }

    public function setFromDatetime($fromDatetime) {
        $this->fromDatetime_ = $fromDatetime;
    }

    public function setToDatetime($toDatetime) {
        $this->toDatetime_ = $toDatetime;
    }

    public function setCompanyId(int $companyId) {
        $this->companyId_ = $companyId;
    }

    public function build() {
        $queryBuilder = $this->queryBuilder_
                                    ->join('vendor_categories', 'vendor_products.category_id', '=', 'vendor_categories.id')
                                    ->join('invoice_products', 'vendor_products.id', '=', 'invoice_products.product_id')
                                    ->select([ 
                                              'invoice_products.created_at', 
                                              'vendor_categories.id as category_id', 
                                              'vendor_categories.name as category_name', 
                                              DB::raw('SUM(invoice_products.total) as total_amount')]);

        if(!empty($this->productIds_)) {
            $queryBuilder->whereIn('vendor_products.id', $this->productIds_);
        }

        if($this->companyId_ > 0) {
            $queryBuilder->where('vendor_products.company_id', $this->companyId_);
        }
        
        if($this->fromDatetime_ != null) {
            $queryBuilder->where('vendor_products.created_at', '>=', $this->fromDatetime_);
        }
        if($this->toDatetime_ != null ) {
            $queryBuilder->where('vendor_products.created_at','<=',$this->toDatetime_);
        }
        $queryBuilder->groupBy('vendor_categories.id',DB::raw('DAY(invoice_products.created_at)'))
        ->orderBy('category_id', 'ASC');
        return $queryBuilder;
    }

    public function reset() {
        $this->queryBuilder_ = $this->queryBuilder_->newInstance();
    }

}
?>