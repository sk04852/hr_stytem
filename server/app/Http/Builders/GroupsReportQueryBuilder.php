<?php
namespace App\Http\Builders;

use App\Http\Builders\Builder;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class GroupsReportQueryBuilder implements Builder {

    private $queryBuilder_;
    private $request_;

    public function __construct(BaseModel $queryBuilder, $request) {
        $this->queryBuilder_ = $queryBuilder;
        $this->request_ = $request;
    }

    public function build() {
        $queryBuilder = $this->queryBuilder_::from('vendor_groups as vg')
                        ->leftJoin('vendor_categories as vc', 'vc.group_id','=', 'vg.id')
                        ->leftJoin('vendor_products as vp', 'vp.group_id','=', 'vg.id')
                        ->select(['vg.code','vg.created_at','vg.name', DB::raw('COUNT(vp.group_id) as total_products'), 
                                            DB::raw('COUNT(vc.group_id) as total_categories')])
                        ->groupBy("vg.id")
                        ->when( !empty($this->request_->company_id), function($queryBuilder_) {
                            return $queryBuilder_->where('vg.company_id', $this->request_->company_id);
                        })
                        ->when( !empty($this->request_->outlet_id), function($queryBuilder_) {
                            return $queryBuilder_->where('vg.outlet_id', $this->request_->outlet_id);
                        })
                        ->when(!empty($this->request_->from), function ($queryBuilder_) {
                            return  $queryBuilder_->whereDate('vg.created_at', '>=', $this->request_->from);
                        })
                        ->when(!empty($this->request_->to), function ($queryBuilder_) {
                            return  $queryBuilder_->whereDate('vg.created_at', '<=', $this->request_->to);
                        });               
        return $queryBuilder;
    }

    public function reset() {
        $this->queryBuilder_ = $this->queryBuilder_->newInstance();
    }

}
?>

