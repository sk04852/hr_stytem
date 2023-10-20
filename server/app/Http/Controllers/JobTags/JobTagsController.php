<?php

namespace App\Http\Controllers\JobTags;

use App\Http\Controllers\JobTags\Models\JobTag;
use App\Http\Controllers\JobTags\Requests\CreateJobTagRequest;
use App\Http\Controllers\JobTags\Requests\DeleteJobTagRequest;
use App\Http\Controllers\JobTags\Requests\UpdateJobTagRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class JobTagsController extends Controller
{
    const MODULE_NAME = 'JobTag';
    const JOB_TAG_CREATED = 'New JobTag created successfully';
    const JOB_TAG_NOT_CREATED = 'Error in creating a JobTag';
    const COLLECTION_NAME = 'JobTags';
    const JOB_TAG_UPDATED = 'JobTag updated successfully';
    const JOB_TAG_NOT_UPDATED = 'Error in updating JobTag';
    const JOB_TAG_DELETED = 'JobTag deleted successfully';
    const JOB_TAG_NOT_DELETED = 'Error in deleting JobTag';
    const JOB_TAG_ALREADY_MARKED = 'JobTag already marked';
    const JOB_TAG_NOT_FOUND = 'JobTag not found';

    public function __construct(JobTag $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $JobTag = $this->model->when(!empty($request->JobTag), function ($query) use ($request) {
                return $query->where('JobTag-ID', $request->JobTag);
            });
              if ($JobTag) {
                  $JobTag = $this->model
                ->when(!empty($request->JobTag), function ($query) use ($request) {
                    return $query->where('JobTag-ID', $request->JobTag);
                })
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());
                  if ($JobTag->isNotEmpty()) {
                      return $this->created([JobTagsController::COLLECTION_NAME => $JobTag]);
                  }
              }
            return $this->noRecord(['message' => JobTagsController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateJobTagRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => JobTagsController::JOB_TAG_CREATED]);
            }
            return $this->failed(['message' => JobTagsController::JOB_TAG_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateJobTagRequest $request)
    {
        try {
            $updatedJobTag = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedJobTag) {
                return $this->created(['message' => JobTagsController::JOB_TAG_UPDATED]);
            }
            return $this->failed(['message' => JobTagsController::JOB_TAG_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteJobTagRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => JobTagsController::JOB_TAG_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    }

