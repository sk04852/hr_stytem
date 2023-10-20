<?php

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\Jobs\Filters\SearchFilter;
use App\Http\Controllers\Jobs\Requests\DeleteJobVideoRequest;
use App\Http\Controllers\Jobs\Requests\ShowJobRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Jobs\Models\Job;
use App\Http\Controllers\Jobs\Models\JobPr;
use App\Http\Controllers\Jobs\Models\JobsFile;
use App\Http\Controllers\Jobs\Requests\CreateJobRequest;
use App\Http\Controllers\Jobs\Requests\DeleteJobRequest;
use App\Http\Controllers\Jobs\Requests\UpdateJobRequest;
use App\Http\Controllers\Jobs\Service\JobService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use function GuzzleHttp\json_decode;

class JobsController extends Controller
{
    //updated for candidate count
    const MODULE_NAME = 'Job';
    const COLLECTION_NAME = 'Jobs';

    const JOB_CREATED = 'New Job created successfully';
    const JOB_NOT_CREATED = 'Error in creating a Job';
    const JOB_UPDATED = 'Job updated successfully';
    const JOB_NOT_UPDATED = 'Error in updating Job';
    const JOB_FILE_NOT_FOUND = 'Job file not found';
    const JOB_FILE_DELETED = 'Job file deleted successfully';
    const JOB_DELETED = 'Job deleted successfully';
    const JOB_NOT_DELETED = 'Error in deleting Job';
    const JOB_ALREADY_MARKED = 'Job already marked';
    const JOB_NOT_FOUND = 'Job not found';
    const JOB_VIDEO_FILE_DELETED = 'Job video file deleted successfully';
    const JOB_VIDEO_FILE_NOT_DELETED = 'Error in deleting Job video file';

    public function __construct(JobPr $model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        try {
            $jobs = QueryBuilder::for($this->model)
                ->select([
                    'jobs_pr.id AS id',
                    'jobs_pr.created_at',
                    'jobs_pr.status',
                    DB::raw('
                        (
                            CASE
                            WHEN jobs_pr.status = '. JobPr::STATUS_ACTIVE .' THEN "Aktiivne"
                            WHEN jobs_pr.status = '. JobPr::STATUS_INACTIVE .' THEN "Mitteaktiivne"
                            WHEN jobs_pr.status = '. JobPr::STATUS_HOLD .' THEN "Ootel"
                            END
                        ) AS status_name
                    '),
                    'jobs_pr.deadline',
                    'jobs_pr.training',
                    'jobs_pr.required_candidates',
                    'jobs_pr.job_type',
                    // 'jobs_pr.',
                    // 'jobs_pr.',
                    // 'jobs_pr.',
                    // 'jobs_pr.',
                    'j.id AS job_id',
                    'j.offer_name',
                    'j.location',
                    'cp.id AS company_pr_id',
                    'c.name AS company_name',
                    DB::raw('COUNT(cj.candidatecv_id) AS job_applicats_count')
                ])
                ->leftJoin('candidatecv_jobs AS cj', function ($query) {
                    $query->on('jobs_pr.id', '=', 'cj.job_pr_id');
                    $query->whereNull('cj.deleted_at');
                })
                ->join('companies_pr AS cp', function ($query) {
                    $query->on('jobs_pr.company_pr_id', '=', 'cp.id');
                    $query->whereNull('cp.deleted_at');
                })
                ->join('companies AS c', function ($query) {
                    $query->on('cp.id', '=', 'c.company_pr_id');
                    $query->where('c.locale', app()->getLocale());
                    $query->whereNull('c.deleted_at');
                })
                ->join('jobs AS j', function ($query) {
                    $query->on('jobs_pr.id', '=', 'j.job_pr_id');
                    $query->whereNull('j.deleted_at');
                })
                ->groupBy('jobs_pr.id')
                // ->with([
                //     'jobs',
                //     'companyPr.company'
                // ])
                ->allowedFilters([
                    AllowedFilter::partial('title', 'j.offer_name'),
                    AllowedFilter::partial('location', 'j.location'),
                    AllowedFilter::exact('job_type', 'job_type'),
                    AllowedFilter::partial('company', 'c.name'),
                    AllowedFilter::custom('search', new SearchFilter)
                ])
                ->defaultSort('-jobs_pr.id')
                ->allowedSorts(['jobs_pr.id', 'offer_name', 'location', 'job_type', 'status', 'company_name', 'created_at', 'deadline', 'training', 'required_candidates', 'job_applicats_count'])
                ->paginate($this->getPerPage())
                ->appends(request()->query());

            if ($jobs->isNotEmpty()) {
                return $this->created(['data' => $jobs]);
            }

            return $this->noRecord(['message' => JobsController::RECORD_NOT_FOUND, 'data' => []], 200);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getActiveJobs(Request $request, JobService $job_service)
    {
        try {

            $active_jobs_array = $job_service->getActiveJobs([
                'jobs',
                'companyPr.company',
            ]);

            return $this->created(['data' => $active_jobs_array]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    // public function getCandidates(Request $request, JobService $job_service, $job_pr_id)
    // {
    //     try {

    //         $response = $job_service->getCandidates($job_pr_id);
    //         if ($response->isEmpty()) {
    //             return $this->created(['candidates' => []]);
    //         }

    //         $statistics = array(
    //             'total' => $response->count(),
    //             "1" => $response->where('action_id', 1)->whereNull('deleted_at')->count(),
    //             "2" => $response->where('action_id', 2)->whereNull('deleted_at')->count(),
    //             "3" => $response->where('action_id', 3)->whereNull('deleted_at')->count(),
    //             "4" => $response->where('action_id', 4)->whereNull('deleted_at')->count(),
    //             "5" => $response->where('action_id', 5)->whereNull('deleted_at')->count(),
    //             "6" => $response->where('action_id', 6)->whereNull('deleted_at')->count(),
    //             "7" => $response->where('action_id', 7)->whereNull('deleted_at')->count(),
    //             "8" => $response->where('action_id', 8)->whereNull('deleted_at')->count(),
    //         );

    //         return $this->created(['candidates' => $response, 'statistics' => $statistics]);
    //     } catch (ModelNotFoundException $ex) {
    //         Log::error($ex);
    //         return $this->noRecord(['message' => $ex->getMessage()]);
    //     } catch (Exception $ex) {
    //         Log::error($ex);
    //         return $this->serverError($ex);
    //     }
    // }

    public function getCandidates(Request $request, JobService $job_service, $job_pr_id, $action_id = null)
    {
        try {
            $candidates = QueryBuilder::for($this->model)
                ->select([
                    'c.id AS candidatecv_id',
                    'c.first_name',
                    'c.last_name',
                    'c.email',
                    'c.phone',
                    'c.gender',
                    'c.location',
                    DB::raw('CONCAT("' . asset('storage') . '", "/", c.photo) AS photo'),
                    'cj.action_id',
                    'a.name AS action_name',
                    'cj.created_at AS created_at',
                    DB::raw('GROUP_CONCAT(ct.type) AS job_type')
                ])
                ->rightJoin('candidatecv_jobs AS cj', function ($query) {
                    $query->on('jobs_pr.id', '=', 'cj.job_pr_id');
                    $query->whereNull('cj.deleted_at');
                })
                ->join('candidatecv AS c', function ($query) {
                    $query->on('cj.candidatecv_id', '=', 'c.id');
                    $query->where('c.status', CandidateCV::STATUS_ACTIVE);
                    $query->whereNull('c.deleted_at');
                })
                ->leftJoin('candidate_job_types AS cjt', function ($query) {
                    $query->on('c.id', '=', 'cjt.candidatecv_id');
                })
                ->leftJoin('job_types AS ct', function ($query) {
                    $query->on('cjt.job_type_id', '=', 'ct.id');
                })
                ->leftJoin('actions AS a', function ($query) {
                    $query->on('cj.action_id', '=', 'a.id');
                    $query->whereNull('a.deleted_at');
                })
                ->groupBy('c.id')
                ->where('jobs_pr.id', $job_pr_id);

                // dd($candidates);

            $filtered_records = $candidates->get();

            $candidates = $candidates->when(!is_null($action_id), function ($query) use ($action_id) {

                $query->where('cj.action_id', (int)$action_id);
            })
                ->allowedFilters([
                    AllowedFilter::partial('first_name', 'c.first_name'),
                    AllowedFilter::partial('last_name', 'c.last_name'),
                    AllowedFilter::exact('gender', 'c.gender'),
                    AllowedFilter::partial('location', 'c.location'),
                    AllowedFilter::partial('phone', 'c.phone'),
                    AllowedFilter::exact('status', 'cj.action_id')
                ])
                ->defaultSort('-c.id')
                ->allowedSorts(['c.id', 'first_name', 'last_name', 'location', 'gender', 'action_name'])
                ->paginate($this->getPerPage())
                ->appends(request()->query());

            if ($filtered_records->isNotEmpty()) {
                $statistics = array(
                    'total' => $filtered_records->count(),
                    "1" => $filtered_records->where('action_id', 1)->count(),
                    "2" => $filtered_records->where('action_id', 2)->count(),
                    "3" => $filtered_records->where('action_id', 3)->count(),
                    "4" => $filtered_records->where('action_id', 4)->count(),
                    "5" => $filtered_records->where('action_id', 5)->count(),
                    "6" => $filtered_records->where('action_id', 6)->count(),
                    "7" => $filtered_records->where('action_id', 7)->count(),
                    "8" => $filtered_records->where('action_id', 8)->count(),
                );
            } else {
                $statistics = array(
                    'total' => $candidates->count(),
                    "1" => 0,
                    "2" => 0,
                    "3" => 0,
                    "4" => 0,
                    "5" => 0,
                    "6" => 0,
                    "7" => 0,
                    "8" => 0,
                );
            }

            return $this->created(['candidates' => $candidates, 'statistics' => $statistics]);
        } catch (ModelNotFoundException $ex) {
            Log::error($ex);
            return $this->noRecord(['message' => $ex->getMessage()]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function store(CreateJobRequest $request, JobService $job_service)
    {
        try {
            $job_pr_onject = $job_service->createJob($request);
            if ($job_pr_onject instanceof JobPr) {
                JobPr::enableSearchSyncing();
                return $this->created(['message' => JobsController::JOB_CREATED]);
            }

            JobPr::enableSearchSyncing();
            return $this->failed(['message' => JobsController::JOB_NOT_CREATED]);
        } catch (Exception $ex) {
            JobPr::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function show(ShowJobRequest $request, JobService $job_service, $id)
    {
        try {
            $include = $request->get('include');

            $job = $job_service->getFullJob($id, $include);

            if (is_null($job)) {
                return $this->failed(['message' => JobsController::RECORD_NOT_FOUND]);
            } else {
                return $this->created(['job' => $job]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function update(UpdateJobRequest $request, JobService $job_service)
    {
        try {
            $response = $job_service->updateJob($request);
            if ($response) {
                JobPr::enableSearchSyncing();
                return $this->created(['message' => JobsController::JOB_UPDATED]);
            } else {
                JobPr::enableSearchSyncing();
                return $this->failed(['message' => JobsController::JOB_NOT_UPDATED]);
            }
        } catch (Exception $ex) {
            JobPr::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteJobRequest $request, JobService $job_service)
    {
        try {
            $job_pr_id = $request->id;
            if ($job_service->deleteJob($job_pr_id)) {
                JobPr::enableSearchSyncing();
                return $this->created(['message' => JobsController::JOB_DELETED]);
            }

            JobPr::enableSearchSyncing();
            return $this->failed(['message' => JobsController::JOB_NOT_DELETED]);
        } catch (Exception $ex) {
            JobPr::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function deleteVideo(DeleteJobVideoRequest $request, JobService $job_service)
    {
        try {
            $job_video_id = $request->job_video_id;
            if ($job_service->deleteJobVideo($job_video_id)) {
                return $this->created(['message' => JobsController::JOB_VIDEO_FILE_DELETED]);
            }

            return $this->failed(['message' => JobsController::JOB_VIDEO_FILE_NOT_DELETED]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    //     public function findBestMatch(Request $request, $job_id)
    //     {
    //         try {
    //             $response_data = collect([]);
    //             $url = env('AI_URL') . '/db_match_all';
    // //            $url = env('AI_URL') . '/match_all';
    //             $body = json_encode([
    //                 "cv_job_switch" => 0,
    //                 "index_id" => (int)$job_id,
    //                 "columns" => []
    //             ]);
    //             $response = Http::withBody($body, 'application/json')->withOptions(['verify'=>false])->post($url);
    //             $decoded_response = json_decode($response);
    //             if(is_null($decoded_response)){
    //                 return $this->created(['jobs' => $response_data]);
    //             }
    //             if (is_array($decoded_response) && !empty($decoded_response)) {
    //                 foreach ($decoded_response as $item) {
    //                     if ($item->cv_id && (int)$item->cv_id != 0) {
    //                         $temp_data = CandidateCV::with('action')->where('id', (int)$item->cv_id)->first();
    //                         if(!is_null($temp_data)){
    //                             $temp_data['percentage'] = $item->confidence;
    //                             $response_data->push($temp_data);
    //                         }
    //                     }
    //                 }
    //                 return $this->created(['candidatecv' => $response_data]);
    //             } else {
    //                 return $this->failed(['message' => $decoded_response]);
    //             }
    //         } catch (Exception $ex) {
    //             Log::error($ex);
    //             return $this->serverError($ex);
    //         }
    //     }

    public function findBestMatch(Request $request, $job_id, JobService $job_service)
    {
        try {

            $jobpr_object = $job_service->getFullJob($job_id);
            if (is_null($jobpr_object)) {
                throw new ModelNotFoundException(JobsController::RECORD_NOT_FOUND);
            }

            $job_candidates_id = $job_service->getCandidates($job_id)->pluck('id');
            $query = [];

            $query['bool']['filter'][] = [
                "term" => [
                    'status' => CandidateCV::STATUS_ACTIVE
                ]
            ];

            if ($job_candidates_id->isNotEmpty())
                $query['bool']['must_not'][] = [
                    "ids" => [
                        'values' => $job_candidates_id
                    ]
                ];


            $job_object = $jobpr_object->jobs->first();
            if (!is_null($job_object->offer_name)) {
                $query['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $job_object->offer_name,
                        'fields' => [
                            "desired_job^10",
                            "education.speciality^7",
                            "job_histories.designation^7",
                            "personal_information^6",
                            "additional_courses.description^3",
                        ]
                    ]
                ];
            }

            if (!is_null($job_object->benefits)) {
                $query['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $job_object->benefits,
                        'fields' => [
                            "personal_information"
                        ]
                    ]
                ];
            }

            if (!is_null($job_object->description)) {
                $query['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $job_object->description,
                        'fields' => [
                            "job_histories.description^9",
                            "education.speciality^7",
                            "job_histories.designation^7",
                            "desired_job^7",
                            "education.additonal_information^6",
                            "driving_licenses.level^4",
                            "additional_courses.description^3",
                            // "age^3"
                        ]
                    ]
                ];
            }

            if (!is_null($job_object->location)) {
                $query['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $job_object->location,
                        'fields' => [
                            "desired_job_location^10",
                            "location^9"
                        ]
                    ]
                ];
            }

            if (!is_null($jobpr_object->workinglanguages->isNotEmpty())) {
                $search_languages = $jobpr_object->workinglanguages->map(function ($data) {
                    return $data->name;
                })->reject(function ($value) {
                    return empty($value);
                });

                $query['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $search_languages->implode(', '),
                        'fields' => [
                            "languages"
                        ]
                    ]
                ];
            }

            if (!is_null($jobpr_object->desiredlanguages->isNotEmpty())) {
                $search_languages = $jobpr_object->desiredlanguages->map(function ($data) {
                    return $data->name;
                })->reject(function ($value) {
                    return empty($value);
                });

                $query['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $search_languages->implode(', '),
                        'fields' => [
                            "languages"
                        ]
                    ]
                ];
            }

            if (!is_null($job_object->requirements)) {
                $query['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $job_object->requirements,
                        'fields' => [
                            "job_histories.description^10",
                            "gender_name^10",
                            "driving_licenses.level^10",
                            "education.level^10",
                            "education.additonal_information^9",
                            "additional_courses.description^7",
                            // "age^7",
                            "personal_information^7"
                        ]
                    ]
                ];
            }

            if (!is_null($job_object->comments)) {
                $query['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $job_object->comments,
                        'fields' => [
                            "driving_licenses.level^10",
                            // "age^7",
                            "source^5",
                            "nationalities^4",
                        ]
                    ]
                ];
            }

            // dd($query);

            $result = CandidateCV::searchQuery($query)->execute();
            // dd($result);

            $data['hits'] = $result->hits();
            // $data['models'] = $result->models();
            // $data['documents'] = $result->documents();
            // $data['highlights'] = $result->highlights();

            return $this->created(['data' => $data]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function downloadFile($id)
    {
        try {
            $file = JobsFile::findOrFail((int)$id);
            
            $pathinfo = pathinfo(Storage::url($file->getRawOriginal('path'), PATHINFO_EXTENSION));
            if (!is_null($file)) {
                return response()->download('storage/' . $file->getRawOriginal('path'), $file->file_name);
            } else {
                return $this->noRecord(['message' => JobsController::JOB_FILE_NOT_FOUND], 200);
            }
        } catch (ModelNotFoundException $ex){
            Log::error($ex);
            return $this->noRecord(['message' => 'File not found']);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function fileDelete(Request $request, $id)
    {
        try {
            JobsFile::findOrFail($id)->delete();
            return $this->created(['message' => JobsController::JOB_FILE_DELETED]);
        } catch (ModelNotFoundException $ex){
            Log::error($ex);
            return $this->noRecord(['message' => JobsController::JOB_FILE_NOT_FOUND]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function timeline(Request $request, $job_pr_id)
    {
        try {
            $job_pr_object = $this->model::find($job_pr_id);
            if (is_null($job_pr_object)) {
                throw new ModelNotFoundException(Controller::RECORD_NOT_FOUND);
            }

            return $this->created(['timeline' => $job_pr_object->timeline()->with('userPr.user', 'comments')->orderBy('created_at', 'DESC')->get()]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function duplicateJob(Request $request, $job_pr_id, JobService $job_service)
    {
        try {

            $job_pr_object = JobPr::findOrFail($job_pr_id);

            $new_job_pr_object = $job_pr_object->duplicate();
            $new_job_pr_object->load([
                'jobs',
                'workinglanguages',
                'desiredlanguages',
                'shifts',
                'files',
                'videos',
                'shifts'
            ]);

            return $this->created(['message' => 'Job is Cloned Successfull', 'data' => $new_job_pr_object]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
}
