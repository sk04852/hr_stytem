<?php
namespace App\Http\Builders;

use App\Http\Builders\Builder;
use App\Http\Controllers\Transactions\Models\Transaction;
use App\Models\BaseModel;

class ClientStatementReportQueryBuilder implements Builder {

    private $queryBuilder_;
    private $clientId_ = -1;
    private $companyId_ = -1;
    private $fromDatetime_ = null;
    private $toDatetime_ = null;

    public function __construct(BaseModel $queryBuilder) {
        $this->queryBuilder_ = $queryBuilder;
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
        $moduleId = getModuleIdFromEntity($this->queryBuilder_);
        $transactions = Transaction::where('transaction_module_id', $moduleId)
                                    ->where('relation_id', $this->clientId_);
        if($this->companyId_ > 0) {
            $transactions->where('company_id', $this->companyId_);
        }

        if($this->fromDatetime_ != null) {
            $transactions->where('created_at', '>=', $this->fromDatetime_);
        }
        if($this->toDatetime_ != null ) {
            $transactions->where('created_at','<=',$this->toDatetime_);
        }

        return $transactions;
    }

    public function reset() {
        $this->queryBuilder_ = $this->queryBuilder_->newInstance();
    }
}
?>