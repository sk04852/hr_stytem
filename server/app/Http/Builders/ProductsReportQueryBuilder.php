<?php
namespace App\Http\Builders;

use App\Http\Builders\Builder;
use App\Models\BaseModel;

class ProductsReportQueryBuilder implements Builder {

    private $queryBuilder_;
    private $request_;

    public function __construct(BaseModel $queryBuilder, $request) {
        $this->queryBuilder_ = $queryBuilder;
        $this->request_ = $request;
    }

    public function build() {
        // quantity in hand and sold is pending
        $queryBuilder = $this->queryBuilder_::from('vendor_products as vp')
                        ->leftJoin('companies as c', 'c.id', '=', 'vp.company_id' )
                        ->leftJoin('vendor_groups as vg', 'vg.id', '=', 'vp.group_id' )
                        ->leftJoin('vendor_categories as vc', 'vc.id','=', 'vp.category_id')
                        ->leftJoin('generic_measurement_units as gmu', 'gmu.id','=', 'vp.measurement_unit_id')
                        ->leftJoin('suppliers as s', 's.id','=', 'vp.supplier_id')
                        ->leftJoin('generic_vat_codes as gvc', function($q){
                            $q -> on('gvc.id','=', 'vp.purchase_vat_code_id');
                        })
                        ->leftJoin('generic_vat_codes as gvg', function($q){
                            $q -> on('gvg.id','=', 'vp.vat_code_id');
                        })
                        ->select(['c.name as company','o.name as shop', 'vp.code', 'vp.name as product',
                                  'vp.purchase_price', 'vg.name as group','vp.unit_price', 'vp.production_date', 'vp.expiry_date',
                                  'vc.name as category', 'gmu.name as measurement_unit', 's.name as supplier', 'gvc.name as purchase_vat',
                                  'gvg.name as vat','vp.created_at'])
                        ->when( !empty($this->request_->company_id), function($queryBuilder_) {
                            return $queryBuilder_->where('vp.company_id', $this->request_->company_id);
                        })
                        ->when( !empty($this->request_->group_id), function($queryBuilder_) {
                            return $queryBuilder_->where('vp.group_id', $this->request_->group_id);
                        })
                        ->when( !empty($this->request_->category_id), function($queryBuilder_) {
                            return $queryBuilder_->where('vp.category_id', $this->request_->category_id);
                        })
                        ->when( !empty($this->request_->supplier_id), function($queryBuilder_) {
                            return $queryBuilder_->where('vp.supplier_id', $this->request_->supplier_id);
                        })
                        ->when( !empty($this->request_->expiry_date), function($queryBuilder_) {
                            return $queryBuilder_->where('vp.expiry_date', $this->request_->expiry_date);
                        })
                        ->when(!empty($this->request_->from), function ($queryBuilder_) {
                            return  $queryBuilder_->whereDate('vp.created_at', '>=', $this->request_->from);
                        })
                        ->when(!empty($this->request_->to), function ($queryBuilder_) {
                            return  $queryBuilder_->whereDate('vp.created_at', '<=', $this->request_->to);
                        });
        return $queryBuilder;
    }

    public function reset() {
        $this->queryBuilder_ = $this->queryBuilder_->newInstance();
    }

}
?>

