<?php
namespace App\Http\Builders;

use App\Http\Builders\Builder;
use App\Models\BaseModel;

class ClientPurchaseQueryBuilder implements Builder {

    private $queryBuilder_;
    private $clientId_ = -1;
    private $companyId_ = -1;

    public function __construct(BaseModel $queryBuilder) {
        $this->queryBuilder_ = $queryBuilder;
    }

    public function setClientId(int $clientId) {
        $this->clientId_ = $clientId;
    }

    public function setCompanyId(int $companyId) {
        $this->companyId_ = $companyId;
    }

    public function build() {
        $queryBuilder = $this->queryBuilder_
                                    ->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
                                    ->leftJoin('companies', 'companies.id', '=', 'invoices.company_id')
                                    ->select(['clients.first_name', 'clients.last_name', 'clients.mobile','companies.name', 'invoices.invoice_number', 'invoices.total']);

        if($this->clientId_ > 0) {
            $queryBuilder->where('clients.id', $this->clientId_);
        }

        if($this->companyId_ > 0) {
            $queryBuilder->where('companies.id', $this->companyId_);
        }

        return $queryBuilder;
    }

    public function reset() {
        $this->queryBuilder_ = $this->queryBuilder_->newInstance();
    }

}
?>

