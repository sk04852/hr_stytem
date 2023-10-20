<?php

namespace App\Http\Controllers\Subjects;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Subjects\Models\Subject;

class SubjectsController extends Controller
{
    const MODULE_NAME = 'subject';
    const COLLECTION_NAME = 'subjects';
    const SUBJECT_CREATE_SUCCESS = 'Subject created successfully';
    const SUBJECT_CREATE_ERROR = 'Failed to create subject';

    public function __construct(Subject $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $data = $this->model->subjectsFilters($request);
            $class = $data->orderBy($this->getSortBy(), $this->getSort())
                ->paginate($this->getPerPage());
            if ($class->isNotEmpty()) {
                return $this->created([SubjectsController::COLLECTION_NAME => $class]);
            }
            return $this->noRecord([SubjectsController::COLLECTION_NAME => $class]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function fetchSubjectsUsingClassID($classId)
    {
        try {
            $data = $this->model->where('class_id',$classId)->get();
            if ($data) {
                return $this->created([SubjectsController::MODULE_NAME => $data]);
            }
            return $this->noRecord(['message' => SubjectsController::RECORD_NOT_FOUND]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
