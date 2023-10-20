<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Mail\Models\Wildcard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Spatie\QueryBuilder\QueryBuilder;

class WildcardsController extends Controller
{
    const MODULE_NAME = 'Mail';
    const COLLECTION_NAME = 'Wildcards';
    const RECORD_NOT_FOUND = 'Wildcards not found';

    public function __construct(Wildcard $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
//            $wildcards = QueryBuilder::for($this->model)
//                ->allowedFilters(['wildcard_key'])
//                ->defaultSort('id')
//                ->jsonPaginate($this->getPerPage())
//                ->appends(request()->query());

            $wildcards = $this->model->all();

            if ($wildcards->isNotEmpty()) {
                return $wildcards;
            }

            return $this->noRecord(['message' => WildcardsController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

