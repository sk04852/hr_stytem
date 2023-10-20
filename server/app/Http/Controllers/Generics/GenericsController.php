<?php

namespace App\Http\Controllers\Generics;

use App\Exceptions\GenericNotSupportedException;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Generics\Builders\GenericModelBuilder;
use App\Http\Controllers\Generics\Requests\CreateGenericRequest as CreateRequest;
use App\Http\Controllers\Generics\Requests\UpdateGenericRequest as UpdateRequest;

class GenericsController extends Controller
{
    const COLLECTION_NAME = 'generics';
    const ITEM = 'generic';
    const GENERIC_TYPE_NOT_PROVIDED = 'Generic type not found';
    const GENERIC_TYPE_ALREADY_EXISTS = 'Generic type already exists';


    public function __construct(Request $request)
    {
        if (empty($request->route()->parameter('type'))) {
            throw new Exception(GenericsController::GENERIC_TYPE_NOT_PROVIDED);
        }

        $type = $request->route()->parameter('type');
        $genericModelBuilder = new GenericModelBuilder($type);
        $model = $genericModelBuilder->getModel();
        if ($model == null) {
            throw new GenericNotSupportedException('Generic not supported');
        }
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            // these are generic filters
            $data = [];
            $data = $this->model->when(!empty($request->name), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })
            ->when(!empty($request->param_1), function ($query) use ($request) {
                return $query->where('param_1', 'like', '%' . $request->param_1 . '%');
            })
            ->when(!empty($request->param_2), function ($query) use ($request) {
                return $query->where('param_2', 'like', '%' . $request->param_2 . '%');
            })
            ->forThisCompany($this->companyId())
            ->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());

            if ($data->isNotEmpty()) {
                return $this->created([GenericsController::COLLECTION_NAME => $data]);
            }
            return $this->created(['message' => 'No records', GenericsController::COLLECTION_NAME => $data]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $data = $request->only(['name', 'param_1', 'param_2']);
            $data['company_id'] = $this->companyId();
            $result = $this->model->create($data);
            if ($result) {
                return $this->created(['message' => GenericsController::RECORD_CREATED, GenericsController::ITEM=> $result]);
            }
       } catch (Exception $ex) {
           return $this->serverError($ex);
       }
    }

    public function show($type, $id)
    {
        try {
            $data = $this->model->find($id);
            if ($data) {
                return $this->created([GenericsController::COLLECTION_NAME => $data]);
            }
            return $this->noRecord(['message' => GenericsController::RECORD_NOT_FOUND],200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(string $type, int $id, UpdateRequest $request)
    {
        try {
            $data = $request->only(['name', 'param_1', 'param_2']);
            $found = $this->model->find($request->id);
            if(!$found){
                return $this->noRecord(['message'=> GenericsController::RECORD_NOT_FOUND],200);
            }
            $result = $found->update($data);
            if ($result) {
                return $this->created(['message' => GenericsController::RECORD_UPDATED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($type, $id)
    {
        try {
            $generic = $this->model->find($id);
            if ($generic) {
                $generic->delete();
                return $this->created(['message' => GenericsController::RECORD_DELETED]);
            }
            return $this->noRecord(['message' => GenericsController::RECORD_NOT_FOUND],200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

