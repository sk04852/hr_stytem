<?php
namespace App\Http\Builders;

use App\Http\Builders\Builder;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class InvoicesListReportQueryBuilder implements Builder {

    private $queryBuilder_;
    private $request_;

    public function __construct(BaseModel $queryBuilder, $request) {
        $this->queryBuilder_ = $queryBuilder;
        $this->request_ = $request;
    }

    public function build() {
        $queryBuilder = $this->queryBuilder_::from('invoices as i')
                        ->leftJoin('clients as c', 'c.id', '=', 'i.client_id')
                        ->leftJoin('company_general_settings as cgs', 'cgs.company_id', '=', 'i.company_id')
                        ->select(['i.id as invoice_id', 'i.invoice_date', 'i.invoice_number', 'i.payment_type', 'i.tax',
                                  DB::raw('CONCAT(i.total, " ", cgs.default_currency) as total'), 'i.created_at','c.id as customer_id',
                                  DB::raw('CONCAT(c.first_name, " ", c.last_name) as customer')])->orderBy('i.id', 'DESC')
                        ->when( !empty($this->request_->company_id), function($queryBuilder_) {
                            return $queryBuilder_->where('i.company_id', $this->request_->company_id);
                        })
                        ->when( !empty($this->request_->client_id), function($queryBuilder_) {
                            return $queryBuilder_->where('i.client_id', $this->request_->client_id);
                        })
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

