<?php
namespace App\Http\Builders;

use App\Http\Builders\Builder;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class CustomersReportQueryBuilder implements Builder {

    private $queryBuilder_;
    private $request_;

    public function __construct(BaseModel $queryBuilder, $request) {
        $this->queryBuilder_ = $queryBuilder;
        $this->request_ = $request;
    }

    public function build() {
        $queryBuilder = $this->queryBuilder_::from('clients as c')
                        ->leftJoin('invoices as i', 'c.id', '=', 'i.client_id')
                        ->select(['c.id', DB::raw('CONCAT(c.first_name, " ", c.last_name) as customer'),
                                  'c.email', 'c.phone', 'c.mobile', 'c.address', 'c.city', 'c.state','c.created_at',
                                  'c.type', 'c.outstanding_receivables', DB::raw('COUNT(i.client_id) as total_invoices, SUM(i.total) as total_purchases,
                                   SUM(i.balance) as total_balance')])
                        ->groupBy('c.id')
                        ->when( !empty($this->request_->company_id), function($queryBuilder_) {
                            return $queryBuilder_->where('c.company_id', $this->request_->company_id);
                        })
                        ->when( !empty($this->request_->city), function($queryBuilder_) {
                            return $queryBuilder_->where('c.city', 'like','%'. $this->request_->city. '%');
                        })
                        ->when( !empty($this->request_->state), function($queryBuilder_) {
                            return $queryBuilder_->where('c.state', 'like','%'. $this->request_->state. '%');
                        })
                        ->when(!empty($this->request_->from), function ($queryBuilder_) {
                            return  $queryBuilder_->whereDate('c.created_at', '>=', $this->request_->from);
                        })
                        ->when(!empty($this->request_->to), function ($queryBuilder_) {
                            return  $queryBuilder_->whereDate('c.created_at', '<=', $this->request_->to);
                        });
        return $queryBuilder;
    }

    public function reset() {
        $this->queryBuilder_ = $this->queryBuilder_->newInstance();
    }

}
?>

