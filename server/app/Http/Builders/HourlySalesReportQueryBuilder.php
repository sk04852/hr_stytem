<?php
namespace App\Http\Builders;

use App\Http\Builders\Builder;
use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HourlySalesReportQueryBuilder implements Builder {

    private $queryBuilder_;
    private $clientId_ = -1;
    private $companyId_ = -1;
    private $date_ = null;

    public function __construct(BaseModel $queryBuilder) {
        $this->queryBuilder_ = $queryBuilder;
    }

    public function setDate($date) {
        $this->date_ = $date;
    }

    public function setClientId(int $clientId) {
        $this->clientId_ = $clientId;
    }

    public function setCompanyId(int $companyId) {
        $this->companyId_ = $companyId;
    }

    public function build() {
        $queryBuilder = $this->queryBuilder_
                                            ->select([ DB::raw('SUM(total) as total_sales'),
                                                      'created_at',
                                                    ]);
        if($this->clientId_ > 0) {
                $queryBuilder->where('client_id', $this->clientId_);
        }
            
        if($this->companyId_ > 0) {
            $queryBuilder->where('company_id', $this->companyId_);
        }
            
        if($this->date_ != null ) {
            $queryBuilder->whereDay('created_at','=',Carbon::create($this->date_));
        }else{
            $queryBuilder->whereDay('created_at','=', Carbon::today());
        }
        $queryBuilder->groupBy(DB::connection('mysql')->table('invoices')->raw('HOUR(created_at)'))
        ->orderBy('created_at', 'ASC');
        return $queryBuilder;
    }

    public function reset() {
        $this->queryBuilder_ = $this->queryBuilder_->newInstance();
    }

}
?>