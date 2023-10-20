<?php

namespace App\Http\Controllers\CandidateCV;

use App\Events\Timeline\CandidateCV\CandidateCreated;
use App\Events\Timeline\CandidateCV\CandidateTimelineActionTypeSaved;
use App\Events\Timeline\CandidateCV\CandidateUpdated;
use App\Http\Controllers\CandidateCV\Filters\CustomListFilter;
use App\Http\Controllers\CandidateCV\Models\CandidateCVDrivingLicense;
use App\Http\Controllers\CandidateCV\Models\CandidateCVRecommender;
use App\Http\Controllers\CandidateCV\Requests\ApplyToJobRequest;
use App\Http\Controllers\CandidateCV\Requests\GetJobAppliedRequest;
use App\Http\Controllers\CandidateCV\Requests\TimelineActionTypeRequest;
use App\Http\Controllers\Timelines\Models\Timeline;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;
use App\Events\CandidateApplyToJob;
use App\Http\Controllers\Actions\Models\Action;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\CandidateCV\Requests\CreateCandidateCVRequest;
use App\Http\Controllers\CandidateCV\Requests\DeleteCandidateCVRequest;
use App\Http\Controllers\CandidateCV\Requests\ShowCandidateCVRequest;
use App\Http\Controllers\CandidateCV\Requests\UpdateCandidateCVRequest;
use App\Http\Controllers\CandidateCV\Requests\UpdateCandidateActionID;
use App\Http\Controllers\Nationalities\Models\Nationality;
use App\Http\Controllers\Tags\Models\Tag;
use App\Models\History;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Jobs\Models\Job;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Controllers\CandidateCV\Filters\SearchFilter;
use App\Http\Controllers\CandidateCV\Services\CandidateCVService;
use Carbon\Carbon;
use App\Http\Controllers\CandidateCV\Exceptions\AlreadyAppliedException;
use App\Http\Controllers\CandidateCV\Filters\ActionFilter;
use App\Http\Controllers\Jobs\Models\JobPr;
use App\Http\Controllers\Timelines\Traits\TimelineTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use ElasticScoutDriverPlus\Support\Query;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class CandidateCVController extends Controller
{

    use TimelineTrait;

    const MODULE_NAME = 'CandidateCV';
    const CANDIDATE_CV_CREATED = 'New CandidateCV created successfully';
    const CANDIDATE_CV_NOT_CREATED = 'Error in creating a CandidateCV';
    const COLLECTION_NAME = 'CandidateCVs';
    const CANDIDATE_CV_UPDATED = 'CandidateCV updated successfully';
    const CANDIDATE_CV_NOT_UPDATED = 'Error in updating CandidateCV';
    const CANDIDATE_ACTION_UPDATED = 'Candidate status updated successfully';
    const CANDIDATE_NOT_ACTION_UPDATED = 'Candidate status update failed';
    const CANDIDATE_ACTION_REJECTED = 'Candidate status rejected updated successfully';
    const CANDIDATE_ACTION_REJECT_FAIL = 'Candidate status reject failed';
    const CANDIDATE_CV_DELETED = 'CandidateCV deleted successfully';
    const CANDIDATE_CV_NOT_DELETED = 'Error in deleting CandidateCV';
    const CANDIDATE_CV_ALREADY_MARKED = 'CandidateCV already marked';
    const CANDIDATE_CV_NOT_FOUND = 'CandidateCV not found';
    const CANDIDATE_PHOTO_UPLOAD_PATH = 'uploads/candidatecv/photos';
    const CANDIDATE_PHOTO_UPLOAD_DISK = 'candidatecv';
    const CANDIDATE_PHOTO_INVALID_FORMAT = 'Candidate invalid upload files. only (jpg, jpeg, png, bmp, gif, svg, or webp) allowed';
    const CANDIDATE_PHOTO_UPLOAD_ERROR = 'Photo not found';
    const CANDIDATE_AVATAR_PHOTO_PATH = 'uploads/candidatecv/photos/avatar.jpg';
    const CANDIDATE_FILES_DOWNLOAD_DISK = 'candidatecv';
    const CANDIDATE_FILES_DOWNLOAD_PATH = 'uploads/candidatecv/downloads';
    const CANDIDATE_FILES_DOWNLOAD_NOT_FOUND = 'uploads/candidatecv/downloads';
    const CANDIDATE_AlREADY_APPLIED = 'This candidate has already candidated to the job';

    const CANDIDATE_TIMELINE_ACTION_TYPE_SAVED = 'Candidate Timeline Action is saved Successfully';
    const CANDIDATE_TIMELINE_ACTION_TYPE_NOT_SAVED = 'Candidate Timeline Action is not saved';

    public function __construct(CandidateCV $model, CandidateCVService $candidateCVService)
    {
        parent::__construct($model);
        // $this->_candidateService = $candidateCVService;
    }

    public function index(Request $request)
    {
        // return $this->model;
        try {

            $candidates = QueryBuilder::for($this->model)
                ->select(
                    'candidatecv.id',
                    'candidatecv.first_name',
                    'candidatecv.last_name',
                    'candidatecv.email',
                    'candidatecv.phone',
                    'candidatecv.gender',
                    'candidatecv.location',
                    'candidatecv.photo',
                    // DB::raw('CONCAT("' . asset('storage') . '", candidatecv.photo) AS photo'),
                    // 'candidatecv.action_id',
                    // 'a.name as action_name',
                    'candidatecv.created_at AS created_at',
                    DB::raw('GROUP_CONCAT(DISTINCT(ct.type)) AS job_type')
                )
                ->leftJoin('candidate_job_types AS cjt', function ($query) {
                    $query->on('candidatecv.id', '=', 'cjt.candidatecv_id');
                })
                ->leftJoin('job_types AS ct', function ($query) {
                    $query->on('cjt.job_type_id', '=', 'ct.id');
                })
                // ->leftJoin('actions AS a', function ($query) {
                //     $query->on('candidatecv.action_id', '=', 'a.id');
                //     $query->whereNull('a.deleted_at');
                // })
                ->whereNull('candidatecv.deleted_at')
                ->groupBy('candidatecv.id')
                ->with([
                    'jobs' => function ($query) {
                        $query->orderBy('pivot_updated_at', 'DESC');
                        $query->where('candidatecv_jobs.action_id', '<>', Action::RENEWAL_STATUS);
                    }
                ])
                ->allowedFilters([
                    'first_name',
                    'last_name',
                    AllowedFilter::exact('job-type', 'cjt.job_type_id'),
                    AllowedFilter::exact('gender'),
                    AllowedFilter::partial('location'),
                    AllowedFilter::partial('phone', 'candidatecv.phone'),
                    AllowedFilter::custom('status', new ActionFilter),
                    AllowedFilter::custom('search', new SearchFilter),
                    AllowedFilter::custom('custom_list_id', new CustomListFilter)
                ]);

            if (!is_null($request->query('custom_list_id'))) {
                $candidates->join('custom_candidate_lists AS ccl', function ($query) use ($request) {
                    $query->on('candidatecv.id', '=', 'ccl.candidatecv_id');
                    $query->where('ccl.list_name_id', '=', $request->query('custom_list_id'));
                });
            }
            // $filtered_records = $candidates->get();

            $candidates = $candidates->defaultSort('-candidatecv.id')
                ->allowedSorts(['candidatecv.id', 'first_name', 'last_name', 'location', 'gender', 'action_name'])
                ->paginate($this->getPerPage())
                ->appends(request()->query());

            

            if ($candidates->isNotEmpty()) {
                $candidates = $candidates->toArray();
                //                $candidates['statistics'] = $this->_candidateService->getActionsStatistics();
                // $candidates['statistics'] = array(
                //     //                    'total' => DB::table('candidatecv')->whereNull('deleted_at')->count(),
                //     //                    "1" => $filtered_records->where('action_id', 1)->whereNull('deleted_at')->count(),
                //     //                    "2" => $filtered_records->where('action_id', 2)->whereNull('deleted_at')->count(),
                //     //                    "3" => $filtered_records->where('action_id', 3)->whereNull('deleted_at')->count(),
                //     //                    "4" => $filtered_records->where('action_id', 4)->whereNull('deleted_at')->count(),
                //     //                    "5" => $filtered_records->where('action_id', 5)->whereNull('deleted_at')->count(),
                //     //                    "6" => $filtered_records->where('action_id', 6)->whereNull('deleted_at')->count(),
                //     //                    "7" => $filtered_records->where('action_id', 7)->whereNull('deleted_at')->count(),
                //     //                    "8" => $filtered_records->where('action_id', 8)->whereNull('deleted_at')->count(),
                //     'total' => DB::table('candidatecv')->whereNull('deleted_at')->count(),
                //     "1" => DB::table('candidatecv')->where('action_id', 1)->whereNull('deleted_at')->count(),
                //     "2" => DB::table('candidatecv')->where('action_id', 2)->whereNull('deleted_at')->count(),
                //     "3" => DB::table('candidatecv')->where('action_id', 3)->whereNull('deleted_at')->count(),
                //     "4" => DB::table('candidatecv')->where('action_id', 4)->whereNull('deleted_at')->count(),
                //     "5" => DB::table('candidatecv')->where('action_id', 5)->whereNull('deleted_at')->count(),
                //     "6" => DB::table('candidatecv')->where('action_id', 6)->whereNull('deleted_at')->count(),
                //     "7" => DB::table('candidatecv')->where('action_id', 7)->whereNull('deleted_at')->count(),
                //     "8" => DB::table('candidatecv')->where('action_id', 8)->whereNull('deleted_at')->count(),
                // );

                $candidates['statistics'] = DB::table('actions')->select(['id', 'name', 'counts'])->whereNull('deleted_at')->get();

                return $this->created(['data' => $candidates]);
            }

            return $this->noRecord(['message' => CandidateCVController::RECORD_NOT_FOUND, 'data' => []], 200);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getcountdata(Request $request)
    {
        try {
            $data = array(
                'total' => DB::table('candidatecv')->whereNull('deleted_at')->count(),
                "1" => DB::table('candidatecv')->where('action_id', 1)->whereNull('deleted_at')->count(),
                "2" => DB::table('candidatecv')->where('action_id', 2)->whereNull('deleted_at')->count(),
                "3" => DB::table('candidatecv')->where('action_id', 3)->whereNull('deleted_at')->count(),
                "4" => DB::table('candidatecv')->where('action_id', 4)->whereNull('deleted_at')->count(),
                "5" => DB::table('candidatecv')->where('action_id', 5)->whereNull('deleted_at')->count(),
                "6" => DB::table('candidatecv')->where('action_id', 6)->whereNull('deleted_at')->count(),
                "7" => DB::table('candidatecv')->where('action_id', 7)->whereNull('deleted_at')->count(),
                "8" => DB::table('candidatecv')->where('action_id', 8)->whereNull('deleted_at')->count(),
            );
            return response()->json($data);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function store(CreateCandidateCVRequest $request)
    {
        try {

            $data = [];
            $validated_data = $request->except(['tags', 'nationalities', 'driving_licenses', 'recommendations', 'photo', 'job_type']);
            $tags = collect($request->input('tags'));
            $nationalities = $request->input('nationalities');
            $driving_licenses = $request->input('driving_licenses');
            $recommendations = $request->input('recommendations');
            $job_type = $request->input('job_type');

            //Start Upload Photo
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $validated_data['photo'] = $photo->store(CandidateCVController::CANDIDATE_PHOTO_UPLOAD_PATH, CandidateCVController::CANDIDATE_PHOTO_UPLOAD_DISK);
            } else {
                $validated_data['photo'] = CandidateCVController::CANDIDATE_AVATAR_PHOTO_PATH;
            }
            //End

            $validated_data['uuid'] = Str::orderedUuid();
            $validated_data['user_id'] = $this->user()->getAuthIdentifier();
            $this->model->fill($validated_data);
            $response = false;
            DB::transaction(function () use ($tags, $nationalities, $driving_licenses, $recommendations, $job_type, &$response, &$data) {
                CandidateCV::disableSearchSyncing();
                $response = $this->model->save();

                $tags = $tags->merge(array_keys(extract_keywords($this->model->personal_information)));

                if ($tags->isNotEmpty()) {
                    foreach ($tags as $tag) {
                        $temp_tag = Tag::firstOrCreate(['name' => $tag]);
                        $this->model->tags()->attach($temp_tag->id);
                    }
                }


                if (is_array($nationalities) && !empty($nationalities)) {
                    foreach ($nationalities as $nationality) {
                        $temp_nationality = Nationality::where('name', $nationality)->first();
                        if (!is_null($temp_nationality)) {
                            $this->model->nationalities()->attach($temp_nationality->id);
                        }
                    }
                }

                if (is_array($job_type) && !empty($job_type)) {
                    $this->model->jobTypes()->sync($job_type);
                }


                if (is_array($driving_licenses) && !empty($driving_licenses)) {
                    foreach ($driving_licenses as $licens) {
                        $this->model->driving_licenses()->create([
                            'level' => $licens['level'],
                            'issue_date' => (isset($licens['issue_date'])) ? $licens['issue_date'] : null,
                            'expiry_date' => (isset($licens['expiry_date'])) ? $licens['expiry_date'] : null,
                        ]);
                    }
                }

                if (is_array($recommendations) && !empty($recommendations)) {
                    foreach ($recommendations as $recommendation) {
                        $this->model->recommendations()->create([
                            'recommendation' => $recommendation
                        ]);
                    }
                }

                $data['cadidatecv'] = $this->model->toArray();
                $data['cadidatecv']['tags'] = $this->model->tags->toArray();
                $data['cadidatecv']['nationalities'] = $this->model->nationalities->toArray();
                $data['cadidatecv']['driving_licenses'] = $this->model->driving_licenses->toArray();
                $data['cadidatecv']['recommendations'] = $this->model->recommendations->toArray();
                $data['cadidatecv']['job_types'] = $this->model->jobTypes->toArray();

                $this->model->searchable();
                CandidateCreated::dispatch($this->model, 'Candidate Created');
                CandidateCV::enableSearchSyncing();
                DB::table('actions')->where('id', Action::GENERAL_STATUS)->increment('counts');
            });
            $data['message'] = CandidateCVController::CANDIDATE_CV_CREATED;
            if ($response) {
                CandidateCV::enableSearchSyncing();
                return $this->created($data);
            }

            CandidateCV::enableSearchSyncing();
            return $this->failed(['message' => CandidateCVController::CANDIDATE_CV_NOT_CREATED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function show(ShowCandidateCVRequest $request, CandidateCVService $candidatecv_service, $id)
    {
        try {
            $include = $request->get('include');

            $candidate_cv = $candidatecv_service->getFullCandidateCV($id, $include);

            if (is_null($candidate_cv)) {
                return $this->failed(['message' => CandidateCVController::RECORD_NOT_FOUND]);
            } else {
                return $this->created(['cadidatecv' => $candidate_cv]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function update(UpdateCandidateCVRequest $request)
    {
        try {
            //Replace "tag_ids" with "tags"
            $validated_data = $request->except(['tags', 'nationality_ids', 'driving_licenses', 'recommendations', 'photo', 'job_type']);
            // $tag_ids = $request->input('tag_ids');
            $tags = collect($request->input('tags'));
            $nationality_ids = $request->input('nationality_ids');
            $driving_licenses = collect($request->input('driving_licenses'));
            $recommendations = collect($request->input('recommendations'));
            $job_type = $request->input('job_type');

            //Start Upload Photo
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $validated_data['photo'] = $photo->store(CandidateCVController::CANDIDATE_PHOTO_UPLOAD_PATH, CandidateCVController::CANDIDATE_PHOTO_UPLOAD_DISK);
            }
            //End

            $response = false;
            $candidateCv = null;
            DB::transaction(function () use ($request, $tags, $nationality_ids, $driving_licenses, $recommendations, $job_type, $validated_data, &$response, &$candidateCv) {
                CandidateCV::disableSearchSyncing();
                $tag_ids = [];
                $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
                $response = $this->model::find($request->id);
                $response->fill($validated_data);
                if ($response->isDirty()) {
                    $this->recordChanges($response, $timeline_data);
                }
                $response->save();

                $tags = $tags->merge(array_keys(extract_keywords($response->personal_information)));

                $candidateCv = $this->model::where('id', $request->id)->first();

                if ($tags->isNotEmpty()) {
                    foreach ($tags as $tag) {
                        $temp_tag = Tag::firstOrCreate(['name' => $tag]);
                        array_push($tag_ids, $temp_tag->id);
                    }
                } else {
                    $tags = null;
                }

                if (is_null($tags)) {
                    $this->recordSyncChanges($candidateCv->tags()->detach(), $candidateCv, 'tags', 'name', $timeline_data);
                } else {
                    $this->recordSyncChanges($candidateCv->tags()->sync($tag_ids), $candidateCv, 'tags', 'name', $timeline_data);
                }

                if (is_null($nationality_ids)) {
                    $this->recordSyncChanges($candidateCv->nationalities()->detach(), $candidateCv, 'nationalities', 'name', $timeline_data);
                } else {
                    $this->recordSyncChanges($candidateCv->nationalities()->sync($nationality_ids), $candidateCv, 'nationalities', 'name', $timeline_data);
                }
                if (is_array($job_type) && !empty($job_type)) {
                    $this->recordSyncChanges($candidateCv->jobTypes()->sync($job_type), $candidateCv, 'jobTypes', 'type', $timeline_data);
                }


                if ($driving_licenses->isNotEmpty()) {
                    $old_licenses = $candidateCv->driving_licenses;
                    $delete_license = $old_licenses->whereNotIn('id', $driving_licenses->whereNotNull('id')->pluck('id')->all());
                    if ($delete_license->isNotEmpty()) {
                        foreach ($delete_license as $single_license) {
                            $this->recordOneToManyDelete($single_license, 'driving_licenses', 'level', $timeline_data);
                            $single_license->delete();
                        }
                    }

                    $new_licenses = $driving_licenses->whereNull('id');
                    if ($new_licenses->isNotEmpty()) {
                        foreach ($new_licenses as $single_license) {
                            unset($single_license['id']);
                            $this->recordOneToManyNew('driving_licenses', $single_license['level'], $timeline_data);
                            $candidateCv->driving_licenses()->create($single_license);
                        }
                    }

                    $update_licenses = $driving_licenses->whereNotNull('id');
                    if ($update_licenses->isNotEmpty()) {
                        foreach ($update_licenses as $single_update) {
                            $temp_update_license = CandidateCVDrivingLicense::find($single_update['id']);
                            unset($single_update['id']);
                            $temp_update_license->fill($single_update);
                            if ($temp_update_license->isDirty()) {
                                $this->recordOneToManyUpdate($temp_update_license, 'driving_licenses', $timeline_data);
                            }
                            $temp_update_license->save();
                        }
                    }
                } else if ($driving_licenses->isEmpty()) {
                    $old_licenses = $candidateCv->driving_licenses;
                    if ($old_licenses->isNotEmpty()) {
                        foreach ($old_licenses as $single_license) {
                            $this->recordOneToManyDelete($single_license, 'driving_licenses', 'level', $timeline_data);
                            $single_license->delete();
                        }
                    }
                }

                if ($recommendations->isNotEmpty()) {
                    $old_recommendations = $candidateCv->recommendations;
                    $delete_recommendations = $old_recommendations->whereNotIn('id', $recommendations->whereNotNull('id')->pluck('id')->all());
                    if ($delete_recommendations->isNotEmpty()) {
                        foreach ($delete_recommendations as $delete_recommendation) {
                            $this->recordOneToManyDelete($delete_recommendation, 'recommendations', 'recommendation', $timeline_data);
                            $delete_recommendation->delete();
                        }
                    }

                    $new_recommendations = $recommendations->whereNull('id');
                    if ($new_recommendations->isNotEmpty()) {
                        foreach ($new_recommendations as $single_recommendation) {
                            unset($single_recommendation['id']);
                            $this->recordOneToManyNew('recommendations', $single_recommendation['recommendation'], $timeline_data);
                            $candidateCv->recommendations()->create($single_recommendation);
                        }
                    }

                    $update_recommendations = $recommendations->whereNotNull('id');
                    if ($update_recommendations->isNotEmpty()) {
                        foreach ($update_recommendations as $single_recommendation) {
                            $temp_update_recommendation = CandidateCVRecommender::find($single_recommendation['id']);
                            unset($single_recommendation['id']);
                            $temp_update_recommendation->fill($single_recommendation);
                            $this->recordOneToManyUpdate($temp_update_recommendation, 'recommendations', $timeline_data);
                            $temp_update_recommendation->save();
                        }
                    }
                } else if ($recommendations->isEmpty()) {
                    $old_recommendations = $candidateCv->recommendations;
                    if ($old_recommendations->isNotEmpty()) {
                        foreach ($old_recommendations as $delete_recommendation) {
                            $this->recordOneToManyDelete($delete_recommendation, 'recommendations', 'recommendation', $timeline_data);
                            $delete_recommendation->delete();
                        }
                    }
                }

                $candidateCv->searchable();
                CandidateUpdated::dispatch($candidateCv, 'Candidate Updated', $timeline_data['old_values'], $timeline_data['new_values']);
                CandidateCV::enableSearchSyncing();
            });
            if ($response) {
                CandidateCV::enableSearchSyncing();
                return $this->created([
                    'message' => CandidateCVController::CANDIDATE_CV_UPDATED,
                    'data' => (is_null($candidateCv)) ? null : $candidateCv->toArray()
                ]);
            }
            CandidateCV::enableSearchSyncing();
            return $this->failed(['message' => CandidateCVController::CANDIDATE_CV_NOT_UPDATED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function applyToJob(ApplyToJobRequest $request, CandidateCVService $candidatecv_service)
    {

        try {

            if($request->action_id == Action::PLACED_STATUS){
                $response = $candidatecv_service->hireOnJob($request);
            }else{
                $response = $candidatecv_service->applyToJob($request);
            }
            
            if ($response) {
                return $this->created(['message' => CandidateCVController::CANDIDATE_ACTION_UPDATED]);
            } else {
                return $this->failed(['message' => CandidateCVController::CANDIDATE_NOT_ACTION_UPDATED]);
            }
        } catch (AlreadyAppliedException $ex) {
            return $this->failed(['message' => $ex->getMessage()]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function reject_candidate(Request $request)
    {
        try {
            $getActionId = Action::where('name', 'Reject')->first();
            if (!empty($getActionId)) {
                $check_flag = DB::table('timeline')->where('candidate_ID', $request->candidate_ID)->latest('updated_at')->limit(1)->value('action_ID');
                $job_flag = DB::table('timeline')->where('candidate_ID', $request->candidate_ID)->latest('updated_at')->limit(1)->value('job_ID');
                if ($check_flag && $check_flag < (int)$getActionId->id) {
                    $value = $this->model->find($request->candidate_ID);
                    $value->update(array('action_id' => $getActionId->id));
                    DB::table('timeline')->insert([
                        'action_ID' => $getActionId->id,
                        'candidate_ID' => $request->candidate_ID,
                        'userpr_ID' => $request->userpr_ID,
                        'comments' => "",
                        'job_ID' => $job_flag,
                        'date_added' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'action_name' => DB::table('actions')->where('id', $getActionId->id)->value('name'),
                    ]);
                    return $this->created([
                        'message' => CandidateCVController::CANDIDATE_ACTION_UPDATED,
                    ]);
                }
                if ((int)$getActionId->id === 1 && $job_flag != $request->job_ID) {
                    $value = $this->model->find($request->candidate_ID);
                    $value->update(array('action_id' => 8));
                    DB::table('timeline')->insert([
                        'action_ID' => $getActionId->id,
                        'candidate_ID' => $request->candidate_ID,
                        'userpr_ID' => $request->userpr_ID,
                        'comments' => "",
                        'job_ID' => $request->job_ID,
                        'date_added' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'action_name' => DB::table('actions')->where('id', $getActionId->id)->value('name'),
                    ]);
                    $value->update(array('action_id' => $getActionId->id));
                    return $this->created([
                        'message' => CandidateCVController::CANDIDATE_ACTION_UPDATED,
                    ]);
                }
                return $this->failed([
                    'message' => CandidateCVController::CANDIDATE_NOT_ACTION_UPDATED,
                ]);
            } else {
                return $this->failed([
                    'message' => CandidateCVController::CANDIDATE_NOT_ACTION_UPDATED,
                ]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteCandidateCVRequest $request)
    {
        $ids = $request->id;
        try {
            DB::transaction(function () use ($request, $ids) {
                CandidateCV::disableSearchSyncing();
                foreach ($ids as $id) {
                    $candidatecv = $this->model->find($id);
                    if (is_null($candidatecv)) {
                        throw new ModelNotFoundException(CandidateCvController::RECORD_NOT_FOUND);
                    }
                    
                    if($candidatecv->jobs->isNotEmpty()){
                        //Get action Id's in which candidate is in and decrement the value in action phases
                        foreach($candidatecv->jobs as $job){
                            DB::table('actions')->where('id', $job->pivot->action_id)->decrement('counts');
                        }                       
                    }
                    
                    $candidatecv->agrelloResponses()->delete();
                    $candidatecv->timeline()->delete();
                    $candidatecv->jobAppliedBy()->detach();
                    $candidatecv->jobs()->detach();
                    $candidatecv->jobTypes()->detach();
                    $candidatecv->custom_lists()->detach();
                    $candidatecv->autoGenerateURL()->delete();
                    $candidatecv->agreement()->delete();
                    $candidatecv->files()->delete();
                    $candidatecv->languages()->detach();
                    $candidatecv->skills()->detach();
                    $candidatecv->additionalCourses()->delete();
                    $candidatecv->education()->delete();
                    $candidatecv->jobHistory()->delete();
                    $candidatecv->tags()->detach();
                    $candidatecv->recommendations()->delete();
                    $candidatecv->driving_licenses()->delete();
                    $candidatecv->nationalities()->detach();
                    $candidatecv->unsearchable();
                    $candidatecv->delete();
                    //                    DB::table('candidatecv')->where('id', $id)->delete();

                    CandidateCV::enableSearchSyncing();
                    DB::table('actions')->where('id', Action::GENERAL_STATUS)->decrement('counts');
                }
            });
            return $this->created(['message' => CandidateCVController::CANDIDATE_CV_DELETED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }



    public function downloadPdf(Request $request, CandidateCVService $candidatecv_service, $id)
    {
        try {
            $candidate_cv = $candidatecv_service->getFullCandidateCV($id);
            if (is_null($candidate_cv)) {
                $this->failed(['message' => CandidateCVController::RECORD_NOT_FOUND]);
            }

            //            $html = view('pdf-files.candidatecv-pdf', ['candidate_cv' => $candidate_cv])->render();
            //            $dompdf = new Dompdf();
            //            $dompdf->loadHtml($html);
            //            $dompdf->render();
            //            $dompdf->stream();


            $pdf = Pdf::loadView('pdf-files.candidatecv-pdf', ['candidate_cv' => $candidate_cv]);
            $pdf_file_name = "{$candidate_cv->first_name}-{$candidate_cv->last_name}_{$candidate_cv->email}.pdf";
            $path = Storage::disk(CandidateCVController::CANDIDATE_FILES_DOWNLOAD_DISK)->path(CandidateCVController::CANDIDATE_FILES_DOWNLOAD_PATH);
            $file_path = $path . '/' . $pdf_file_name;
            File::ensureDirectoryExists($path);
            $pdf->save($file_path);

            if (File::exists($file_path)) {
                //                return Storage::disk(CandidateCVController::CANDIDATE_FILES_DOWNLOAD_DISK)->download(CandidateCVController::CANDIDATE_FILES_DOWNLOAD_PATH.'/'.$pdf_file_name);
                return response()->download($file_path);
            } else {
                return $this->failed(['message' => CandidateCVController::CANDIDATE_FILES_DOWNLOAD_NOT_FOUND]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    // public function downloadPdf(\Kodebyraaet\Prince\Prince $prince, CandidateCVService $candidatecv_service, $id)
    // {
    //     try {
    //         $candidate_cv = $candidatecv_service->getFullCandidateCV($id);
    //         if (is_null($candidate_cv)) {
    //             $this->failed(['message' => CandidateCVController::RECORD_NOT_FOUND]);
    //         }

    //         $pdf_file_name = "{$candidate_cv->first_name}-{$candidate_cv->last_name}_{$candidate_cv->email}.pdf";
    //         $html = view('pdf-files.candidatecv-pdf', ['candidate_cv' => $candidate_cv])->toHtml();

    //         return $prince->html($html)->download($pdf_file_name);

    //     } catch (Exception $ex) {
    //         return $this->serverError($ex);
    //     }

    // }

    //     public function findBestMatch(Request $request, $candidatecv_id)
    //     {
    //         try {
    //             $response_data = collect([]);
    //             $url = env('AI_URL') . '/db_match_all';
    // //            $url = env('AI_URL') . '/match_all';
    //             $body = json_encode([
    //                 "cv_job_switch" => 1,
    //                 "index_id" => (int)$candidatecv_id,
    //                 "columns" => []
    //             ]);
    //             $response = Http::withBody($body, 'application/json')->withOptions(['verify'=>false])->post($url);
    //             $decoded_response = json_decode($response);
    //             if(is_null($decoded_response)){
    //                 return $this->created(['jobs' => $response_data]);
    //             }
    //             if (is_array($decoded_response) && !empty($decoded_response)) {
    //                 foreach ($decoded_response as $item) {
    //                     if ((int)$item->job_id != 0) {
    //                         $temp_data = Job::where('id', (int)$item->job_id)->first();
    //                         if(!is_null($temp_data)){
    //                             $temp_data['percentage'] = $item->confidence;
    //                             $response_data->push($temp_data);
    //                         }
    //                     }
    //                 }
    //                 return $this->created(['jobs' => $response_data]);
    //             } else {
    //                 return $this->failed(['message' => $decoded_response]);
    //             }
    //         } catch (Exception $ex) {
    //             return $this->serverError($ex);
    //         }
    //     }

    public function findBestMatch(Request $request, $candidatecv_id, CandidateCVService $candidatecv_service)
    {
        try {

            $highlight_fields = collect([]);
            $per_page = $request->get('perPage', 10);

            $candidatecv_object = $candidatecv_service->getFullCandidateCV($candidatecv_id);
            if (is_null($candidatecv_object)) {
                throw new ModelNotFoundException(CandidateCVController::RECORD_NOT_FOUND);
            }

            $applied_jobs = $candidatecv_object->jobs;

            $query = [];


            $query['function_score']['query']['bool']['filter'][] = [
                "term" => [
                    'status' => JobPr::STATUS_ACTIVE
                ]
            ];

            if ($applied_jobs->isNotEmpty()) {
                $query['function_score']['query']['bool']['must_not'][] = [
                    "ids" => [
                        'values' => $applied_jobs->pluck('id')
                    ]
                ];
            }

            // if(!is_null($candidatecv_object->desired_salary)){
            //     $query['bool']['filter'][] = [
            //         "range" => [
            //             'salary_amount_1' => [
            //                 'lte' => $candidatecv_object->desired_salary
            //             ]
            //         ]
            //     ];
            //     $query['bool']['filter'][] = [
            //         "range" => [
            //             'salary_amount_2' => [
            //                 'gte' => $candidatecv_object->desired_salary
            //             ]
            //         ]
            //     ];
            // }


            if (!is_null($candidatecv_object->desired_salary)) {
                $query['function_score']['functions'][] = [
                    'weight' => 1.1,
                    'filter' => [
                        "range" => [
                            'salary_amount_1' => [
                                'lte' => $candidatecv_object->desired_salary
                            ]
                        ]
                    ]
                ];
                $query['function_score']['functions'][] = [
                    'weight' => 1.1,
                    'filter' => [
                        "range" => [
                            'salary_amount_2' => [
                                'gte' => $candidatecv_object->desired_salary
                            ]
                        ]
                    ]
                ];
            }


            if (!is_null($candidatecv_object->gender)) {
                $query['function_score']['query']['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $candidatecv_object->gender_name,
                        "analyzer" => "job_requirements_analyzer",
                        'fields' => [
                            'requirements^5',
                            'comments^4'
                        ]
                    ]
                ];

                $highlight_fields->push([
                    'requirements',
                    'comments'
                ]);
            }

            if (!is_null($candidatecv_object->age)) {
                $query['function_score']['query']['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $candidatecv_object->age,
                        "analyzer" => "job_requirements_analyzer",
                        'fields' => [
                            'requirements^10',
                            'comments^8',
                            'description^5'
                        ]
                    ]
                ];

                $highlight_fields->push([
                    'requirements',
                    'comments',
                    'description'
                ]);
            }

            if (!is_null($candidatecv_object->mother_language) || $candidatecv_object->languages->isNotEmpty()) {
                $query['function_score']['query']['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $candidatecv_object->mother_language . ', ' . $candidatecv_object->languages->implode('name', ', '),
                        "analyzer" => "job_requirements_analyzer",
                        'fields' => [
                            'working_languages^7',
                            'desired_languages^7'
                        ]
                    ]
                ];

                $highlight_fields->push([
                    'working_languages',
                    'desired_languages'
                ]);
            }


            if (!is_null($candidatecv_object->location) || !is_null($candidatecv_object->desired_job_location)) {
                $query['function_score']['query']['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $candidatecv_object->location . ', ' . $candidatecv_object->desired_job_location,
                        "analyzer" => "job_requirements_analyzer",
                        'fields' => [
                            'location^4'
                        ]
                    ]
                ];

                $highlight_fields->push([
                    'location'
                ]);
            }

            if ($candidatecv_object->driving_licenses->isNotEmpty()) {
                $query['function_score']['query']['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $candidatecv_object->driving_licenses->implode('level', ', '),
                        "analyzer" => "job_requirements_analyzer",
                        'fields' => [
                            'requirements^8'
                        ]
                    ]
                ];

                $highlight_fields->push([
                    'requirements'
                ]);
            }

            if ($candidatecv_object->education->isNotEmpty()) {
                $education_levels = $candidatecv_object->education->map(function ($data) {

                    return (is_null($data->education_level)) ? null : $data->education_level->name;
                })->reject(function ($value) {
                    return empty($value);
                });

                if ($education_levels->isNotEmpty()) {
                    $query['function_score']['query']['bool']['should'][] = [
                        "multi_match" => [
                            'query' => $education_levels->implode(', '),
                            "analyzer" => "job_requirements_analyzer",
                            'fields' => [
                                'requirements^7'
                            ]
                        ]
                    ];

                    $highlight_fields->push([
                        'requirements'
                    ]);
                }
            }

            if (!is_null($candidatecv_object->desired_job)) {
                $query['function_score']['query']['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $candidatecv_object->desired_job,
                        "analyzer" => "job_requirements_analyzer",
                        'fields' => [
                            'offer_name^10'
                        ]
                    ]
                ];

                $highlight_fields->push([
                    'offer_name'
                ]);
            }

            if ($candidatecv_object->jobHistory->isNotEmpty()) {
                $job_designations = $candidatecv_object->jobHistory->map(function ($data) {
                    return $data->designation;
                })->reject(function ($value) {
                    return empty($value);
                });

                $query['function_score']['query']['bool']['should'][] = [
                    "multi_match" => [
                        'query' => $job_designations->implode(', '),
                        "analyzer" => "job_requirements_analyzer",
                        'fields' => [
                            'offer_name^10'
                        ]
                    ]
                ];

                $highlight_fields->push([
                    'offer_name'
                ]);
            }


            if ($candidatecv_object->education->isNotEmpty()) {
                $education_speciality = $candidatecv_object->education->map(function ($data) {
                    return $data->speciality;
                })->reject(function ($value) {
                    return empty($value);
                });

                $education_additional_information = $candidatecv_object->education->map(function ($data) {
                    return $data->additional_information;
                })->reject(function ($value) {
                    return empty($value);
                });

                if ($education_speciality->isNotEmpty()) {
                    $query['function_score']['query']['bool']['should'][] = [
                        "multi_match" => [
                            'query' => $education_speciality->implode(', '),
                            "analyzer" => "job_requirements_analyzer",
                            'fields' => [
                                'offer_name^8'
                            ]
                        ]
                    ];

                    $highlight_fields->push([
                        'offer_name'
                    ]);
                }

                if ($education_additional_information->isNotEmpty()) {
                    $query['function_score']['query']['bool']['should'][] = [
                        "multi_match" => [
                            'query' => $education_additional_information->implode(', '),
                            "analyzer" => "job_requirements_analyzer",
                            'fields' => [
                                'requirements^10',
                                'description^7'
                            ]
                        ]
                    ];

                    $highlight_fields->push([
                        'requirements',
                        'description'
                    ]);
                }
            }

            if ($candidatecv_object->additionalCourses->isNotEmpty()) {
                $additional_courses_description = $candidatecv_object->additionalCourses->map(function ($data) {
                    return $data->description;
                })->reject(function ($value) {
                    return empty($value);
                });

                if ($additional_courses_description->isNotEmpty()) {
                    $query['function_score']['query']['bool']['should'][] = [
                        "multi_match" => [
                            'query' => $additional_courses_description->implode(', '),
                            "analyzer" => "job_requirements_analyzer",
                            'fields' => [
                                'requirements^7',
                                'description^5',
                                'offer_name'
                            ]
                        ]
                    ];

                    $highlight_fields->push([
                        'requirements',
                        'description',
                        'offer_name'
                    ]);
                }
            }

            if ($candidatecv_object->jobHistory->isNotEmpty()) {
                $job_history_description = $candidatecv_object->jobHistory->map(function ($data) {
                    return $data->description;
                })->reject(function ($value) {
                    return empty($value);
                });

                if ($job_history_description->isNotEmpty()) {
                    $query['function_score']['query']['bool']['should'][] = [
                        "multi_match" => [
                            'query' => $job_history_description->implode(', '),
                            "analyzer" => "job_requirements_analyzer",
                            'fields' => [
                                'description^10',
                                'requirements^5',
                            ]
                        ]
                    ];

                    $highlight_fields->push([
                        'requirements',
                        'description'
                    ]);
                }
            }

            $highlight_fields = $highlight_fields->flatten()->unique();

            // dd($query);
            $query = JobPr::searchQuery($query);
            if ($highlight_fields->isNotEmpty()) {
                foreach ($highlight_fields as $highlight_field) {
                    // $query->highlight($highlight_field);
                    $query->highlight($highlight_field, [
                        'fragment_size' => 1,
                        'pre_tags' => '<span class="badge badge-primary">',
                        'post_tags' => '</span>'
                    ]);
                }
            }
            $result = $query->paginate($per_page);


            $data['hits'] = $result->hits();
            // dd($data['hits'][0]);
            // $data['models'] = $result->models();
            // $data['documents'] = $result->documents();
            // $data['highlights'] = $result->highlights();

            // dd('hi', $data['hits']);
            // if ($data['hits']->isEmpty()) {
            //     $query = [];
            //     $query['bool']['filter'][] = [
            //         "term" => [
            //             'status' => JobPr::STATUS_ACTIVE
            //         ]
            //     ];
            //     $result = JobPr::searchQuery($query)->paginate($per_page);
            //     $data['hits'] = $result->hits();
            // }

            return $this->created(['data' => $data]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function timeline(Request $request, $candidatecv_id)
    {
        try {
            $candidatecv_object = $this->model::find($candidatecv_id);
            if (is_null($candidatecv_object)) {
                throw new ModelNotFoundException(Controller::RECORD_NOT_FOUND);
            }

            return $this->created(['timeline' => $candidatecv_object->timeline()->with('userPr.user', 'comments')->orderBy('created_at', 'DESC')->get()]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function timelineActionType(TimelineActionTypeRequest $request)
    {
        try {
            CandidateCV::disableSearchSyncing();
            $candidatecv_object = $this->model::find($request->candidatecv_id);
            if (is_null($candidatecv_object)) {
                throw new ModelNotFoundException(Controller::RECORD_NOT_FOUND);
            }

            $candidatecv_object->timeline_action_type_id = $request->timeline_action_type_id;
            $candidatecv_object->timeline_action_type_comment = $request->timeline_action_type_comment;
            if ($candidatecv_object->save()) {
                $candidatecv_object->searchable();
                CandidateTimelineActionTypeSaved::dispatch($candidatecv_object, 'Timeline Action Type Selected');
                CandidateCV::enableSearchSyncing();
                return $this->created(['message' => CandidateCVController::CANDIDATE_TIMELINE_ACTION_TYPE_SAVED]);
            }

            CandidateCV::enableSearchSyncing();
            return $this->failed(['message' => CandidateCVController::CANDIDATE_TIMELINE_ACTION_TYPE_NOT_SAVED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getJobsApplied(GetJobAppliedRequest $request, CandidateCVService $candidatecv_service)
    {
        try {
            $candidatecv_object = $this->model::find($request->id);
            if (is_null($candidatecv_object)) {
                throw new ModelNotFoundException(CandidateCVController::RECORD_NOT_FOUND);
            }

            $data = $candidatecv_service->getAllCandidateJobsRecords($candidatecv_object);

            return $this->created(['data' => $data]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getJobsAppliedPreviousPhase(GetJobAppliedRequest $request, CandidateCVService $candidatecv_service)
    {
        try {
            $candidatecv_object = $this->model::find($request->id);
            if (is_null($candidatecv_object)) {
                throw new ModelNotFoundException(CandidateCVController::RECORD_NOT_FOUND);
            }

            $action_id = null;
            if($request->action_id != 8){
                $action_id = $request->action_id - 1;
            }

            $data = $candidatecv_service->getAllCandidateJobsRecords($candidatecv_object, $action_id);

            return $this->created(['data' => $data]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
}
