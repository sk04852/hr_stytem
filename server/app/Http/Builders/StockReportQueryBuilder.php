<?php
namespace App\Http\Builders;

use App\Http\Builders\Builder;
use App\Models\BaseModel;

class StockReportQueryBuilder implements Builder {

    private $queryBuilder_;
    private $productIds_ = [];
    private $clientId_ = -1;
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

    public function setClientId(int $clientId) {
        $this->clientId_ = $clientId;
    }

    public function setCompanyId(int $companyId) {
        $this->companyId_ = $companyId;
    }

    public function build() {
        $queryBuilder = $this->queryBuilder_
                                    ->leftJoin('master_stocks', 'vendor_products.id', '=', 'master_stocks.product_id')
                                    ->select(['vendor_products.code', 
                                              'vendor_products.name', 
                                              'vendor_products.id', 
                                              'vendor_products.unit_price', 
                                              'vendor_products.purchase_price', 
                                              'master_stocks.available_quantity', 
                                              'master_stocks.expired', 
                                              'master_stocks.damaged']);

        if(!empty($this->productIds_)) {
            $queryBuilder->whereIn('vendor_products.id', $this->productIds_);
        }

        if($this->clientId_ > 0) {
            $queryBuilder->where('vendor_products.client_id', $this->clientId_);
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
        return $queryBuilder;
    }

    public function reset() {
        $this->queryBuilder_ = $this->queryBuilder_->newInstance();
    }

}
?>