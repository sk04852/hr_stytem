<?php

/**
 * CandidateCVService
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\CandidateCV\Services;

use App\Events\InvalidateUrl;
use App\Events\Timeline\CandidateCV\CandidateApplyToJob;
use App\Events\Timeline\CandidateCV\ChangeStatus;
use App\Http\Controllers\Actions\Models\Action;
use App\Http\Controllers\CandidateCV\Exceptions\AlreadyAppliedException;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\CandidateCV\Requests\ApplyToJobRequest;
use App\Models\AutoGenerateURL;
use App\Models\UserPr;
use App\Notifications\CandidateProfileConfirmed;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Notification;
use App\Events\Timeline\CandidateCV\CandidateProfileConfirmed as CandidateProfileConfirmedEvent;
use App\Exceptions\InvalidStatusException;
use App\Exceptions\SameStatusException;

class CandidateCVService
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const MSG_SAME_STATUS_EXCEPTION = "Candidate has the same status, Kindly select another status";
    const MSG_INVALID_STATUS_EXCEPTION = "Invalid Status, Candidate can be moved to next phase or rejected";


    // public function getActionsStatistics()
    // {
    //     return CandidateCV::select(['actions.id', 'actions.name', DB::raw('count(candidatecv.id) as count')])
    //         ->rightJoin('actions', function ($join) {
    //             $join->on('actions.id', '=', 'candidatecv.action_id')->whereNull('actions.deleted_at');
    //         })
    //         ->groupBy('actions.id', 'actions.name')
    //         ->get();
    // }

    public function getFullCandidateCV($id, $include = [])
    {
        $candidate_cv = null;

        if (is_array($include) && !empty($include)) {
            $candidate_cv = CandidateCV::where('id', $id)->with('action')->with($include)->first();
        } else {
            $with_relations = [
                'nationalities',
                'driving_licenses',
                'recommendations',
                'tags',
                'jobHistory',
                'education',
                'education.education_degree',
                'education.education_level',
                'additionalCourses',
                'skills',
                'languages',
                'files',
                'jobTypes',
                'jobs'
            ];

            $candidate_cv = CandidateCV::where('id', $id)->with($with_relations)->first();
        }

        return $candidate_cv;
    }

    public function updateStatus($candidatecv_id, $token, $status = CandidateCVService::STATUS_INACTIVE)
    {
        try {
            $response = null;
            DB::transaction(function () use ($candidatecv_id, $status, $token, &$response) {
                $candidatecv = CandidateCV::where('id', $candidatecv_id)->first();
                if (is_null($candidatecv)) {
                    throw new ModelNotFoundException('Candidate Not found');
                }

                $candidatecv->status = $status;
                $candidatecv->status = 1;
                $candidatecv->consent = 1;
                $response = $candidatecv->save();
                if (!is_null($response) && $response) {
                    $auto_generated_url = $candidatecv->autoGenerateURL()->where('token', $token)->first();
                    if (is_null($auto_generated_url)) {
                        throw new ModelNotFoundException('URL record not found');
                    }

                    InvalidateUrl::dispatch($auto_generated_url);
                    $link_sender = UserPr::where('id', $auto_generated_url->user_pr_id)->first();
                    if (is_null($link_sender)) {
                        throw new ModelNotFoundException('Recruiter who send link is not ');
                    }

                    CandidateProfileConfirmedEvent::dispatch($candidatecv, 'Profile Confirmed by Candidate');

                    Notification::send($link_sender, new CandidateProfileConfirmed([
                        'title' => "$candidatecv->first_name $candidatecv->last_name has confirmed its profile",
                        'image' => $candidatecv->getRawOriginal('photo')
                    ]));
                }
            });

            return $response;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Candidate can apply on single job
     */
    // public function applyToJob(ApplyToJobRequest $request)
    // {
    //     $response = false;
    //     DB::transaction(function () use ($request, &$response) {
    //         $candidatecv = CandidateCV::find($request->candidatecv_id);
    //         if (is_null($candidatecv)) {
    //             throw new ModelNotFoundException('Record Not Found');
    //         }
    //         $old_action_id = $candidatecv->action_id;


    //         if ($candidatecv->jobs->isEmpty()) {
    //             //Apply on Job
    //             $candidatecv->jobs()->attach($request->job_pr_id, [
    //                 'action_id' => $request->action_id,
    //                 'user_pr_id' => Auth::id()
    //             ]);
    //             $candidatecv->update(['action_id' => $request->action_id]);
    //             CandidateApplyToJob::dispatch($candidatecv, $request->job_pr_id, Auth::id(), 'Candidate Apply on Job');
    //         }

    //         if ($candidatecv->jobs->isNotEmpty()) {
    //             $applied_requested_job = $candidatecv->jobs->where('pivot.job_pr_id', $request->job_pr_id)->first();
    //             $previous_jobs_status = $candidatecv->jobs->where('pivot.action_id', '<>', Action::RENEWAL_STATUS);

    //             if (is_null($applied_requested_job)) {
    //                 if ($previous_jobs_status->isEmpty()) {
    //                     //Apply on Job
    //                     $candidatecv->jobs()->attach($request->job_pr_id, [
    //                         'action_id' => $request->action_id,
    //                         'user_pr_id' => Auth::id()
    //                     ]);
    //                     $candidatecv->update(['action_id' => $request->action_id]);
    //                     CandidateApplyToJob::dispatch($candidatecv, $request->job_pr_id, Auth::id(), 'Candidate Apply on Job');
    //                 } else {
    //                     throw new AlreadyAppliedException('This candidate has already applied to the job');
    //                 }
    //             } else {
    //                 $candidatecv->jobs()->updateExistingPivot($request->job_pr_id, [
    //                     'action_id' => $request->action_id,
    //                 ]);
    //                 $candidatecv->update(['action_id' => $request->action_id]);
    //                 ChangeStatus::dispatch($candidatecv, $request->job_pr_id, $old_action_id, Auth::id(), 'Candidate Action Status Changed');
    //             }
    //         }

    //         $response = true;
    //     });

    //     return $response;
    // }


    /**
     * Candidate can apply on multiple job
     */
    public function applyToJob(ApplyToJobRequest $request)
    {
        $response = false;
        DB::transaction(function () use ($request, &$response) {
            $candidatecv = CandidateCV::find($request->candidatecv_id);
            if (is_null($candidatecv)) {
                throw new ModelNotFoundException('Record Not Found');
            }

            //Check if alreday applied on job or not
            $applied_job = $candidatecv->jobs()->where('job_pr_id', $request->job_pr_id)->first();


            //If not applied on same job 
            if (is_null($applied_job)) {
                //Apply on Job
                $candidatecv->jobs()->attach($request->job_pr_id, [
                    'action_id' => $request->action_id,
                    'user_pr_id' => Auth::id()
                ]);

                CandidateApplyToJob::dispatch($candidatecv, $request->job_pr_id, Auth::id(), 'Candidate Apply on Job');
                DB::table('actions')->where('id', $request->action_id)->increment('counts');
                $response = true;
                return;
            }

            // Check if the use select the same status again for the same job
            if ($request->action_id == $applied_job->pivot->action_id) {
                throw new SameStatusException(self::MSG_SAME_STATUS_EXCEPTION);
            }

            $old_action_id = $applied_job->pivot->action_id;
            $candidate_next_phase = $applied_job->pivot->action_id + 1;
            //Can update to only next phase or reject or if rejected then can only update status 2
            if ($request->action_id == $candidate_next_phase || $request->action_id == Action::RENEWAL_STATUS || ($applied_job->pivot->action_id == Action::RENEWAL_STATUS && $request->action_id == Action::APPLIED_STATUS)) {
                $candidatecv->jobs()->updateExistingPivot($request->job_pr_id, [
                    'action_id' => $request->action_id,
                ]);
                ChangeStatus::dispatch($candidatecv, $request->job_pr_id, $old_action_id, $request->action_id, Auth::id(), 'Candidate Action Status Changed');
                DB::table('actions')->where('id', $old_action_id)->decrement('counts');
                DB::table('actions')->where('id', $request->action_id)->increment('counts');
                $response = true;
                return;
            } else {
                throw new InvalidStatusException(self::MSG_INVALID_STATUS_EXCEPTION);
            }

            $response = true;
        });

        return $response;
    }

    public function hireOnJob($request)
    {
        $response = false;
        DB::transaction(function () use ($request, &$response) {
            $candidatecv = CandidateCV::find($request->candidatecv_id);
            if (is_null($candidatecv)) {
                throw new ModelNotFoundException('Record Not Found');
            }

            //Check if alreday applied on job or not
            $selected_applied_job = $candidatecv->jobs()->where('job_pr_id', $request->job_pr_id)->first();
            $rejected_applied_jobs = $candidatecv->jobs()->where('job_pr_id', '<>', $request->job_pr_id)->get();

            if (!is_null($selected_applied_job)) {
                // Check if the use select the same status again for the same job
                if ($request->action_id == $selected_applied_job->pivot->action_id) {
                    throw new SameStatusException(self::MSG_SAME_STATUS_EXCEPTION);
                }

                $old_action_id = $selected_applied_job->pivot->action_id;
                if ($selected_applied_job->pivot->action_id != 6) {
                    throw new InvalidStatusException(self::MSG_INVALID_STATUS_EXCEPTION);
                }

                $candidatecv->jobs()->updateExistingPivot($request->job_pr_id, [
                    'action_id' => $request->action_id,
                ]);
                ChangeStatus::dispatch($candidatecv, $request->job_pr_id, $old_action_id, $request->action_id, Auth::id(), 'Candidate Action Status Changed');
                DB::table('actions')->where('id', $old_action_id)->decrement('counts');
                DB::table('actions')->where('id', $request->action_id)->increment('counts');
            }

            if ($rejected_applied_jobs->isNotEmpty()) {
                foreach ($rejected_applied_jobs as $reject_job) {

                    $old_action_id = $reject_job->pivot->action_id;
                    $candidatecv->jobs()->updateExistingPivot($reject_job->id, [
                        'action_id' => Action::RENEWAL_STATUS,
                    ]);
                    ChangeStatus::dispatch($candidatecv, $request->job_pr_id, $old_action_id, Action::RENEWAL_STATUS, Auth::id(), 'Candidate Action Status Changed');
                    DB::table('actions')->where('id', $old_action_id)->decrement('counts');
                    DB::table('actions')->where('id', Action::RENEWAL_STATUS)->increment('counts');
                }
            }
            
            $response = true;
        });

        return $response;
    }

    /**
     * This function check if candidate applied on any job.
     *
     * @param CandidateCV $candidateCV
     * @return mixed|null
     */
    protected function isCandidateApplied(CandidateCV $candidateCV)
    {
        return $candidateCV->jobs()->where('action_id', '<>', Action::RENEWAL_STATUS)->latest('updated_at')->first();
    }

    /**
     * This function help to maintain only single candidate on single job
     *
     * @param CandidateCV $candidateCV
     * @param $job_pr_id
     * @return mixed|null
     */
    protected function getAppliedJobRecord(CandidateCV $candidateCV, $job_pr_id)
    {
        return $candidateCV->jobs()->where('job_pr_id', $job_pr_id)->latest('updated_at')->first();
    }

    public function getAllCandidateJobsRecords(CandidateCV $candidatecv_object, $action_id = null)
    {

        $jobs = DB::table('candidatecv_jobs AS cp')
            ->select(DB::raw("cp.job_pr_id, cp.candidatecv_id, cp.action_id, a.name as action_name, j.offer_name, j.location, jp.job_type, (case when jp.status = 1 then 'Active' when jp.status = 2 then 'On Hold' ELSE 'Inactive' END) as status, cp.created_at as applied_date, cp.updated_at as change_status"))
            ->join('jobs_pr AS jp', function ($join) {
                $join->on('cp.job_pr_id', '=', 'jp.id')
                    ->whereNull('jp.deleted_at');
            })
            ->join('jobs AS j', function ($join) {
                $join->on('jp.id', '=', 'j.job_pr_id')
                    ->whereNull('j.deleted_at');
            })
            ->join('actions AS a', function ($join) {
                $join->on('cp.action_id', '=', 'a.id')
                    ->whereNull('a.deleted_at');
            })
            ->where('cp.candidatecv_id', '=', $candidatecv_object->id)
            ->when(!is_null($action_id), function ($query) use ($action_id) {
                return $query->where('cp.action_id', $action_id);
            })
            ->orderBy('cp.updated_at', 'DESC')
            ->get();

        return $jobs;
    }
}
