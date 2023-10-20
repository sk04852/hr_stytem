<?php

namespace App\Observers;

use App\Http\Controllers\Accounts\Models\Account;
use App\Http\Services\GlobalSearchService;

class AccountObserver
{
    private $globalSearchService_;

    public function __construct(GlobalSearchService $globalSearchService) {
        $this->globalSearchService_ = $globalSearchService;
    }

    public function created(Account $account)
    {

    }

    public function updated(Account $account)
    {
        //
    }

    public function deleted(Account $account)
    {
        //
    }

    public function forceDeleted(Account $account)
    {
        //
    }
}
