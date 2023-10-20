<?php

namespace App\Http\Controllers\Topics;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Topics\Models\Topic;

class TopicsController extends Controller
{
    const MODULE_NAME = 'topic';
    const COLLECTION_NAME = 'topics';
    const SUBJECT_CREATE_SUCCESS = 'Topic created successfully';
    const SUBJECT_CREATE_ERROR = 'Failed to create topic';

    public function __construct(Topic $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $data = $this->model->topicsFilters($request);
            $class = $data->orderBy($this->getSortBy(), $this->getSort())
                ->paginate($this->getPerPage());
            if ($class->isNotEmpty()) {
                return $this->created([TopicsController::COLLECTION_NAME => $class]);
            }
            return $this->noRecord([TopicsController::COLLECTION_NAME => $class]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function fetchTopicsUsingSubjectID($subjectId)
    {
        try {
            $data = $this->model->where('subject_id',$subjectId)->get();
            if ($data) {
                return $this->created([TopicsController::MODULE_NAME => $data]);
            }
            return $this->noRecord(['message' => TopicsController::RECORD_NOT_FOUND]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
