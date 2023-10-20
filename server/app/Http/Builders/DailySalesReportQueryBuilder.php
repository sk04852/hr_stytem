<?php
namespace App\Http\Builders;

use App\Http\Builders\Builder;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class DailySalesReportQueryBuilder implements Builder {

    private $queryBuilder_;
    private $request_;

    public function __construct(BaseModel $queryBuilder, $request) {
        $this->queryBuilder_ = $queryBuilder;
        $this->request_ = $request;
    }

    public function build() {
        $queryBuilder = $this->queryBuilder_::from('invoices as i')
                        ->leftJoin('companies as c', 'c.id', '=', 'i.company_id')
                        ->select([DB::raw('COUNT(i.company_id) as total_invoices, SUM(i.total) as total'), 'c.name as company'])
                        ->groupBy('i.company_id')
                        ->whereDate('i.created_at', date('Y-m-d'))         
                        ->when(!empty($this->request_->from), function ($queryBuilder_) {
                            return  $queryBuilder_->whereDate('i.created_at', '>=', $this->request_->from);
                        })
                        ->when(!empty($this->request_->to), function ($queryBuilder_) {
                            return  $queryBuilder_->whereDate('i.created_at', '<=', $this->request_->to);
                        });               
        return $queryBuilder;
    }

    public function reset() {
        $this->queryBuilder_ = $this->queryBuilder_->newInstance();
    }

}
?>

