<?php

namespace App\Http\Controllers\EducationDegrees;

use App\Http\Controllers\EducationDegrees\Models\EducationDegree;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class EducationDegreesController extends Controller
{
    const MODULE_NAME = 'EducationDegrees';
    const COLLECTION_NAME = 'Education_Degrees';
    const RECORD_NOT_FOUND = 'Education Degrees not found';

    public function __construct(EducationDegree $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {

            $education_degrees = $this->model::all();
            if ($education_degrees->isNotEmpty()) {
                return $this->created([EducationDegreesController::COLLECTION_NAME => $education_degrees]);
            }

            return $this->noRecord(['message' => EducationDegreesController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

