<?php
namespace App\Http\Builders;

use App\Http\Builders\Builder;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class CategoriesReportQueryBuilder implements Builder {

    private $queryBuilder_;
    private $request_;

    public function __construct(BaseModel $queryBuilder, $request) {
        $this->queryBuilder_ = $queryBuilder;
        $this->request_ = $request;
    }

    public function build() {
        $queryBuilder = $this->queryBuilder_::from('vendor_categories as vc')
                        ->leftJoin('companies as c', 'c.id', '=', 'vc.company_id' )
                        ->leftJoin('vendor_products as vp', 'vc.id', '=', 'vp.category_id' )
                        ->select(['vc.code','vc.name', 'c.name as company_name', 'vc.created_at',  DB::raw('COUNT(vp.category_id) as total_products')])
                        ->groupBy('vc.id')
                        ->when( !empty($this->request_->company_id), function($queryBuilder_) {
                            return $queryBuilder_->where('vc.company_id', $this->request_->company_id);
                        })
                        ->when( !empty($this->request_->group_id), function($queryBuilder_) {
                            return $queryBuilder_->where('vc.group_id', $this->request_->group_id);
                        })
                        ->when(!empty($this->request_->from), function ($queryBuilder_) {
                            return  $queryBuilder_->whereDate('vc.created_at', '>=', $this->request_->from);
                        })
                        ->when(!empty($this->request_->to), function ($queryBuilder_) {
                            return  $queryBuilder_->whereDate('vc.created_at', '<=', $this->request_->to);
                        });               
        return $queryBuilder;
    }

    public function reset() {
        $this->queryBuilder_ = $this->queryBuilder_->newInstance();
    }

}
?>

