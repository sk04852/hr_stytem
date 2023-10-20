<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Companies\Models\CompanyIndustry;
use App\Http\Controllers\Companies\Requests\IndustrySuggestionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Spatie\QueryBuilder\QueryBuilder;

class IndustriesController extends Controller
{
    const MODULE_NAME = 'Company';
    const COLLECTION_NAME = 'Industries';
    const RECORD_NOT_FOUND = 'Industries not found';

    public function __construct(CompanyIndustry $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $industries = QueryBuilder::for($this->model)
                ->allowedFilters(['name'])
                ->defaultSort('id')
                ->get();

            if ($industries->isNotEmpty()) {
                return $industries;
            }

            return $this->noRecord(['message' => IndustriesController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function suggestions (IndustrySuggestionRequest $request){
        try {
            $industry_name = $request->input('industry_name');

            $industries = $this->model::where('name', 'Like', "%$industry_name%")->whereNull('deleted_at')->get();

            if ($industries->isNotEmpty()) {
                return $industries;
            }

            return $this->noRecord(['message' => IndustriesController::RECORD_NOT_FOUND], 200);

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

