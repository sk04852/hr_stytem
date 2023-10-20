<?php

namespace App\Http\Controllers\Nationalities;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Nationalities\Models\Nationality;
use Illuminate\Http\Request;
use Exception;
use Spatie\QueryBuilder\QueryBuilder;

class NationalitiesController extends Controller
{
    const MODULE_NAME = 'Nationalities';
    const COLLECTION_NAME = 'Nationalities';
    const NATIONALITIES_NOT_FOUND = 'Nationalities not found';

    public function __construct(Nationality $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $nationalities = $this->model->all(['id', 'name']);

            if ($nationalities->isNotEmpty()) {
                return $nationalities;
            }

            return $this->noRecord(['message' => NationalitiesController::RECORD_NOT_FOUND], 200);

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

