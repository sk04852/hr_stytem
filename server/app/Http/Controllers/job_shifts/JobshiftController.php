<?php

namespace App\Http\Controllers\job_shifts;

use App\Http\Controllers\job_shifts\Models\job_shifts;
use App\Http\Controllers\job_shifts\Requests\CreateJobShifts;
use App\Http\Controllers\job_shifts\Requests\UpdateJobShifts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class JobshiftController extends Controller
{
    const MODULE_NAME = 'job_shifts_lang';
    const job_shifts_lang_CREATED = 'New job_shifts_lang created successfully';
    const job_shifts_lang_NOT_CREATED = 'Error in creating a job_shifts_lang';
    const COLLECTION_NAME = 'job_shifts_lang';
    const job_shifts_lang_UPDATED = 'job_shifts_lang updated successfully';
    const job_shifts_lang_NOT_UPDATED = 'Error in updating job_shifts_lang';
    const job_shifts_lang_DELETED = 'job_shifts_lang deleted successfully';
    const job_shifts_lang_NOT_DELETED = 'Error in deleting job_shifts_lang';
    const job_shifts_lang_ALREADY_MARKED = 'job_shifts_lang already marked';
    const job_shifts_lang_NOT_FOUND = 'job_shifts_lang not found';

    public function __construct(job_shifts $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $job_shifts_lang = $this->model->when(!empty($request->job_shifts_lang), function ($query) use ($request) {
                return $query->where('job_shifts_langID', $request->job_shifts_lang);
            });
              if ($job_shifts_lang) {
                  $job_shifts_lang = $this->model
                ->when(!empty($request->job_shifts_lang), function ($query) use ($request) {
                    return $query->where('job_shifts_langID', $request->job_shifts_lang);
                })
               ->paginate($this->perPage = 500000);
                  if ($job_shifts_lang->isNotEmpty()) {
                      return $this->created([JobshiftController::COLLECTION_NAME => $job_shifts_lang]);
                  }
              }
            return $this->noRecord(['message' => JobshiftController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }

    public function store(CreateJobShifts $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => jobshiftController::job_shifts_lang_CREATED]);
            }
            return $this->failed(['message' => jobshiftController::job_shifts_lang_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
        public function destroy(UpdateJobShifts $request)
            {
                try {
                    $this->findOneById($request->id)->delete();
                    return $this->created(['message' => jobshiftController::job_shifts_lang_DELETED]);
                } catch (Exception $ex) {
                    return $this->serverError($ex);
                }
            }
    }


