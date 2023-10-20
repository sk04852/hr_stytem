<?php

namespace App\Http\Controllers\PeopleLookup;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PeopleLookup\Models\PeopleLookup as ThisModel;
use App\Http\Controllers\PeopleLookup\Requests\PeopleLookupRequest; 

class PeopleLookupController extends Controller
{
    const MODULE_NAME = 'peopleLookup';
    const COLLECTION_NAME = 'peopleLookups';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function lookUp(PeopleLookupRequest $request) {

        try {
            $query = $this->model->lookup($request->get('q'));
            if($request->has('type') && $request->get('type') != "any")
                $query->type($request->get('type'));
            $results = $query->get();
            return $this->created(['results'=> $results]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

}