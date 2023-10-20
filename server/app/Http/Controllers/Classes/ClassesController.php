<?php

namespace App\Http\Controllers\Classes;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Classes\Models\Classs;
use App\Http\Controllers\Classes\Requests\CreateClassRequest;

class ClassesController extends Controller
{
    const MODULE_NAME = 'class';
    const COLLECTION_NAME = 'classes';
    const CLASS_CREATE_SUCCESS = 'Class created successfully';
    const CLASS_CREATE_ERROR = 'Failed to create class';

    public function __construct(Classs $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $data = $this->model->classesFilters($request);
            $class = $data->orderBy($this->getSortBy(), $this->getSort())
                ->paginate($this->getPerPage());
            if ($class->isNotEmpty()) {
                return $this->created([ClassesController::COLLECTION_NAME => $class]);
            }
            return $this->noRecord([ClassesController::COLLECTION_NAME => $class], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateClassRequest $request)
    {
        try {
            $data = $request->all();
            $this->model = $this->model->fill($data);
            if ($this->model->save()) {
                return $this->created(["message" => ClassesController::CLASS_CREATE_SUCCESS, "class" => $this->model]);
            } else {
                return $this->failed(["error" => ClassesController::CLASS_CREATE_ERROR]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
