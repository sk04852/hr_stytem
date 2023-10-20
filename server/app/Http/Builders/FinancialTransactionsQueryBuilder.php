<?php

namespace App\Http\Builders;

use App\Http\Builders\Builder;
use App\Models\BaseModel;

class FinancialTransactionsQueryBuilder implements Builder
{

    private $queryBuilder_;
    private $request_;

    public function __construct(BaseModel $queryBuilder, $request)
    {
        $this->queryBuilder_ = $queryBuilder;
        $this->request_ = $request;
    }

    public function build()
    {
        $queryBuilder = $this->queryBuilder_::from('monetary_transactions as mt')
            ->leftJoin('transaction_types as tt', 'mt.transaction_type_id', '=', 'tt.id')
            ->leftJoin('users as u', 'mt.assigned_to', '=', 'u.id')
            ->select(
                [
                    'mt.trading_account_id as account_id',
                    'mt.account_name',
                    'mt.amount',
                    'tt.name as transaction_type',
                    'mt.date',
                    'mt.crm_comment',
                    'u.first_name as assigned_to',
                    'mt.created_at',

                ]
            )
            ->when(!empty($this->request_->trading_account_id), function ($queryBuilder_) {
                return $queryBuilder_->where('mt.trading_account_id', $this->request_->trading_account_id);
            })
            ->when(!empty($this->request_->assigned_to), function ($queryBuilder_) {
                return $queryBuilder_->where('mt.assigned_to', $this->request_->assigned_to);
            })
            ->when(!empty($this->request_->transaction_type_id), function ($queryBuilder_) {
                return $queryBuilder_->where('mt.transaction_type_id', $this->request_->transaction_type_id);
            })
            ->when(!empty($this->request_->from), function ($queryBuilder_) {
                return  $queryBuilder_->whereDate('mt.created_at', '>=', $this->request_->from);
            })
            ->when(!empty($this->request_->to), function ($queryBuilder_) {
                return  $queryBuilder_->whereDate('mt.created_at', '<=', $this->request_->to);
            });
        return $queryBuilder;
    }

    public function reset()
    {
        $this->queryBuilder_ = $this->queryBuilder_->newInstance();
    }
}
