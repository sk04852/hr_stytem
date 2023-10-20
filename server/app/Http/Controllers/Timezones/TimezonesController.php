<?php

namespace App\Http\Controllers\Timezones;

use App\Http\Controllers\Timezones\Models\Timezone;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class TimezonesController extends Controller
{
    const MODULE_NAME = 'timezones';
    const COLLECTION_NAME = 'Timezones';
    const RECORD_NOT_FOUND = 'Timezones not found';

    public function __construct(Timezone $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {

            $timezones = $this->model::all();
            if ($timezones->isNotEmpty()) {
                return $this->created([TimezonesController::COLLECTION_NAME => $timezones]);
            }

            return $this->noRecord(['message' => TimezonesController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

