<?php

namespace App\Http\Controllers\JobActivity;

use App\Http\Controllers\JobActivity\Models\JobActivity;
use App\Http\Controllers\JobActivity\Requests\CreateJobActivityRequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class JobActivityController extends Controller
{
    const MODULE_NAME = 'JobActivity';
    const JOBActivity_CREATED = 'New JobActivity created successfully';
    const JOBActivity_NOT_CREATED = 'Error in creating a JobActivity';
    const COLLECTION_NAME = 'JobActivity';
    const JOBActivity_UPDATED = 'JobActivity updated successfully';
    const JOBActivity_NOT_UPDATED = 'Error in updating JobActivity';
    const JOBActivity_DELETED = 'JobActivity deleted successfully';
    const JOBActivity_NOT_DELETED = 'Error in deleting JobActivity';
    const JOBActivity_ALREADY_MARKED = 'JobActivity already marked';
    const JOBActivity_NOT_FOUND = 'JobActivity not found';

    public function __construct(JobActivity $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $JobActivity = $this->model->when(!empty($request->JobActivity), function ($query) use ($request) {
                return $query->where('JobActivityID', $request->JobActivity);
            });
              if ($JobActivity) {
                  $JobActivity = $this->model
                ->when(!empty($request->JobActivity), function ($query) use ($request) {
                    return $query->where('JobActivityID', $request->JobActivity);
                })
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());
                  if ($JobActivity->isNotEmpty()) {
                      return $this->created([JobActivityController::COLLECTION_NAME => $JobActivity]);
                  }
              }
            return $this->noRecord(['message' => JobActivityController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }
    }

