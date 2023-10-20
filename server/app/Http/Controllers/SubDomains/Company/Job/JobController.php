<?php

/**
 * Job
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\SubDomains\Company\Job;

use App\Events\InvalidateUrl;
use App\Http\Controllers\Companies\Models\CompanyContact;
use App\Http\Controllers\Companies\Models\CompanyPr;
use App\Http\Controllers\Jobs\Models\JobPr;
use App\Http\Controllers\Jobs\Requests\CreateJobLinkRequest;
use App\Http\Controllers\Jobs\Service\JobService;
use App\Models\UserPr;
use App\Notifications\PostJobLink;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class JobController extends BaseController
{
    public function createJob(Request $request, $company_pr_id, $token)
    {
        try {
            $decrypted_company_pr_id = Crypt::decryptString($company_pr_id);

            $company_pr = CompanyPr::where('id', $decrypted_company_pr_id)->with('company', 'companyContacts', 'companylocations')->first();

            return view('sub-domains/company/job/create-job', ['company_pr' => $company_pr, 'token' => $token]);
        } catch (DecryptException $exception) {
            Log::error($exception);
            return response()->json(
                [
                    'error' => $exception->getMessage(),
                    'ex' => $exception,
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );
        } catch (Exception $exception) {
            Log::error($exception);
            return response()->json(
                [
                    'error' => $exception->getMessage(),
                    'ex' => $exception,
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function store(CreateJobLinkRequest $request, JobService $job_service, $token)
    {
        try {
            $company_pr_id = CompanyPr::where('id', $request->company_pr_id)->first();
            if (is_null($company_pr_id)) {
                throw new ModelNotFoundException('Company Not found');
            }

            $auto_generated_url = $company_pr_id->autoGenerateURL()->where('token', $token)->first();
            if (is_null($auto_generated_url)) {
                throw new ModelNotFoundException('URL record not found');
            }

            $user_pr = UserPr::find($auto_generated_url->user_pr_id);
            if (is_null($user_pr)) {
                throw new ModelNotFoundException('User not found who sent the link');
            }

            $company_contact = CompanyContact::find($auto_generated_url->company_contact_id);
            if (is_null($company_contact)) {
                throw new ModelNotFoundException('The Person to whom job link sent, Not Found');
            }

            $job_pr_onject = $job_service->createJob($request);
            if ($job_pr_onject instanceof JobPr) {
                $job_pr_onject->auto_generate_url_id = $auto_generated_url->id;
                $job_pr_onject->save();

                $job = $job_pr_onject->jobs->first();
                $job->comments = $job->comments . '::' . $job_pr_onject->desired_start_time;
                $job->save();

                Notification::send($user_pr, new PostJobLink($job_pr_onject, $company_contact));

                InvalidateUrl::dispatch($auto_generated_url);

                return response()->json(['message' => 'Job is Posted Successfully']);
            } else {
                return view();
            }
        } catch (Exception $exception) {
            Log::error($exception);
            return response()->json(
                [
                    'error' => $exception->getMessage(),
                    'ex' => $exception,
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    public function verifyJob(Request $request, JobService $job_service, $job_pr_id, $token)
    {
        try {
            $decrypted_job_pr_id = Crypt::decryptString($job_pr_id);

            $job_pr = $job_service->getFullJob($decrypted_job_pr_id);

            return view('sub-domains/company/job/verify-job', ['job_pr' => $job_pr, 'token' => $token, 'job_pr_id' => $job_pr_id]);
        } catch (DecryptException $exception) {
            Log::error($exception);
            return response()->json(
                [
                    'error' => $exception->getMessage(),
                    'ex' => $exception,
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );
        } catch (Exception $exception) {
            Log::error($exception);
            return response()->json(
                [
                    'error' => $exception->getMessage(),
                    'ex' => $exception,
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function verifiedJob(Request $request, JobService $job_service, $job_pr_id, $token){
        try {
            $decrypted_job_pr_id = Crypt::decryptString($job_pr_id);
            $response = $job_service->updateStatus($decrypted_job_pr_id, $token, JobPr::STATUS_ACTIVE, $request);

            if($response){
                return response()->json(['message' => 'Status is Successfully Updated', 'response' => $response]);
            }else{
                return response()->json(['error' => 'Unable to Update Status'], 422);
            }

        } catch (ModelNotFoundException $exception) {
            Log::error($exception);
            return $this->noRecord(['error' => $exception->getMessage()]);

        } catch (DecryptException $exception) {
            Log::error($exception);
            return response()->json(
                [
                    'error' => $exception->getMessage(),
                    'ex' => $exception,
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );

        } catch (Exception $exception) {
            Log::error($exception);
            return response()->json(
                [
                    'error' => $exception->getMessage(),
                    'ex' => $exception,
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
