<?php

namespace App\Http\Controllers\job_desired_lang;

use App\Http\Controllers\job_desired_lang\Models\job_desired_lang;
use App\Http\Controllers\job_desired_lang\Requests\CreateJobDesiredLang;
use App\Http\Controllers\job_desired_lang\Requests\UpdateJobDesiredLang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class JobDesiredLangController extends Controller
{
    const MODULE_NAME = 'job_desired_lang';
    const job_desired_lang_CREATED = 'New job_desired_lang created successfully';
    const job_desired_lang_NOT_CREATED = 'Error in creating a job_desired_lang';
    const COLLECTION_NAME = 'job_desired_lang';
    const job_desired_lang_UPDATED = 'job_desired_lang updated successfully';
    const job_desired_lang_NOT_UPDATED = 'Error in updating job_desired_lang';
    const job_desired_lang_DELETED = 'job_desired_lang deleted successfully';
    const job_desired_lang_NOT_DELETED = 'Error in deleting job_desired_lang';
    const job_desired_lang_ALREADY_MARKED = 'job_desired_lang already marked';
    const job_desired_lang_NOT_FOUND = 'job_desired_lang not found';

    public function __construct(job_desired_lang $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $job_desired_lang = $this->model->when(!empty($request->job_desired_lang), function ($query) use ($request) {
                return $query->where('job_desired_langID', $request->job_desired_lang);
            });
              if ($job_desired_lang) {
                  $job_desired_lang = $this->model
                ->when(!empty($request->job_desired_lang), function ($query) use ($request) {
                    return $query->where('job_desired_langID', $request->job_desired_lang);
                })
            ->paginate($this->perPage = 500000);
                  if ($job_desired_lang->isNotEmpty()) {
                      return $this->created([JobDesiredLangController::COLLECTION_NAME => $job_desired_lang]);
                  }
              }
            return $this->noRecord(['message' => JobDesiredLangController::job_desired_lang_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }

    public function store(CreateJobDesiredLang $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => JobDesiredLangController::job_desired_lang_CREATED]);
            }
            return $this->failed(['message' => JobDesiredLangController::job_desired_lang_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
       public function destroy(UpdateJobDesiredLang $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => JobDesiredLangController::job_desired_lang_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    }


