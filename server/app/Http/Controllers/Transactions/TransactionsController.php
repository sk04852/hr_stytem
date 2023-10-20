<?php
namespace App\Http\Controllers\Transactions;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Transactions\Models\Transaction as ThisModel;


class TransactionsController extends Controller
{
    const MODULE_NAME = 'transaction';
    const COLLECTION_NAME = 'transactions';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
        dd(12);
    }

    
}

?>