<?php

namespace App\Http\Controllers\Policies;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Policies\Models\Policy as ThisModel;
use App\Http\Controllers\Policies\Requests\CreatePolicyRequest as CreateRequest;
use App\Http\Controllers\Policies\Requests\UpdatePolicyRequest as UpdateRequest;
use phpDocumentor\Reflection\Types\This;

class PoliciesController extends Controller
{
    const MODULE_NAME = 'policies';
    const COLLECTION_NAME = 'policies';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        try {
            $records = DB::table('policies as p')
            ->join('policies_categories as pc', 'p.id', 'pc.id')
            ->get(['p.id', 'p.policy_name', 'pc.category_name']);
            return $this->created([PoliciesController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function create()
    {
        return;
    }

    public function store(CreateRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if($this->model->save()) {
                return $this->created([PoliciesController::MODULE_NAME => $this->model, 'message'=> PoliciesController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->findOneById($id);
            return $this->created([PoliciesController::MODULE_NAME => $record]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function edit($id)
    {
        return;
    }

    public function update(UpdateRequest $request, $id)
    {
        try {
            $auth = auth()->user();
            $record = $this->findOneById($id);
            $record->fill($request->all());
            if($record->save()) {
                return $this->created([PoliciesController::MODULE_NAME => $record, 'message'=> PoliciesController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        return;
    }


}
