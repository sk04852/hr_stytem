<?php

namespace App\Http\Controllers\EducationDegrees;

use App\Http\Controllers\EducationDegrees\Models\EducationLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class EducationLevelsController extends Controller
{
    const MODULE_NAME = 'EducationLevels';
    const COLLECTION_NAME = 'Education_Levels';
    const RECORD_NOT_FOUND = 'Education Level not found';

    public function __construct(EducationLevel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {

            $education_levels = $this->model::all();
            if ($education_levels->isNotEmpty()) {
                return $this->created([EducationLevelsController::COLLECTION_NAME => $education_levels]);
            }

            return $this->noRecord(['message' => EducationLevelsController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

