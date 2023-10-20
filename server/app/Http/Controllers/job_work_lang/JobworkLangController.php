<?php

namespace App\Http\Controllers\job_work_lang;

use App\Http\Controllers\job_work_lang\Models\job_work_lang;
use App\Http\Controllers\job_work_lang\Requests\CreateJobworkLang;
use App\Http\Controllers\job_work_lang\Requests\UpdateJobWorkLang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class JobworkLangController extends Controller
{
    const MODULE_NAME = 'job_work_lang';
    const job_work_lang_CREATED = 'New job_work_lang created successfully';
    const job_work_lang_NOT_CREATED = 'Error in creating a job_work_lang';
    const COLLECTION_NAME = 'job_work_lang';
    const job_work_lang_UPDATED = 'job_work_lang updated successfully';
    const job_work_lang_NOT_UPDATED = 'Error in updating job_work_lang';
    const job_work_lang_DELETED = 'job_work_lang deleted successfully';
    const job_work_lang_NOT_DELETED = 'Error in deleting job_work_lang';
    const job_work_lang_ALREADY_MARKED = 'job_work_lang already marked';
    const job_work_lang_NOT_FOUND = 'job_work_lang not found';

    public function __construct(job_work_lang $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $job_work_lang = $this->model->when(!empty($request->job_work_lang), function ($query) use ($request) {
                return $query->where('job_work_langID', $request->job_work_lang);
            });
              if ($job_work_lang) {
                  $job_work_lang = $this->model
                ->when(!empty($request->job_work_lang), function ($query) use ($request) {
                    return $query->where('job_work_langID', $request->job_work_lang);
                })
                 ->paginate($this->perPage = 500000);
                  if ($job_work_lang->isNotEmpty()) {
                      return $this->created([JobworkLangController::COLLECTION_NAME => $job_work_lang]);
                  }
              }
            return $this->noRecord(['message' => JobworkLangController::job_work_lang_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }

    public function store(CreateJobworkLang $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => JobworkLangController::job_work_lang_CREATED]);
            }
            return $this->failed(['message' => JobworkLangController::job_work_lang_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

     public function destroy(UpdateJobWorkLang $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => JobworkLangController::job_work_lang_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

// public function update(UpdateJobWorkLang $request)
//     {
//         try {
//             $updatedJob = $this->model::where('id', $request->id)->update($request->all());
//             if ($updatedJob) {
//                 return $this->created(['message' => job_work_lang_UPDATED::JOB_UPDATED]);
//             }
//             return $this->failed(['message' => job_work_lang_NOT_UPDATED::JOB_NOT_UPDATED]);
//         } catch (Exception $ex) {
//             return $this->serverError($ex);
//         }
//     }


    }


