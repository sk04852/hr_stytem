<?php

namespace App\Http\Controllers\Mail;

use App\Exceptions\ZoomAccountUnverified;
use App\Exceptions\ZoomIDNotFound;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\Companies\Models\CompanyPr;
use App\Http\Controllers\Mail\Models\Template;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Jobs\Models\JobPr;
use App\Http\Controllers\Users\Models\User;
use App\Lib\TemplateParser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\Mail\Requests\Template\CreateTemplateRequest;
use App\Http\Controllers\Mail\Requests\Template\UpdateTemplateRequest;
use App\Http\Controllers\Mail\Requests\Template\DeleteTemplateRequest;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Users\Services\UserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class TemplatesController extends Controller
{
    const MODULE_NAME = 'Mail';
    const COLLECTION_NAME = 'Templates';
    const RECORD_NOT_FOUND = 'Templates not found';
    const TEMPLATE_CREATED = 'New template created successfully';
    const TEMPLATE_NOT_CREATED = 'Error in creating a template';
    const TEMPLATE_UPDATED = 'Template updated successfully';
    const TEMPLATE_NOT_UPDATED = 'Error in updating template';
    const TEMPLATE_DELETED = 'Template deleted successfully';
    const TEMPLATE_NOT_DELETED = 'Error in deleting template';

    public function __construct(Template $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $templates = QueryBuilder::for($this->model)
                ->allowedFilters(['title', 'subject', 'template_key'])
                ->defaultSort('-id')
                ->allowedSorts(['id', 'title'])
                ->paginate($this->getPerPage())
                ->appends(request()->query());

            if ($templates->isNotEmpty()) {
                return $templates;
            }

            return $this->noRecord(['message' => TemplatesController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store (CreateTemplateRequest $request){
        try {
            $validated_data = $request->all();
            $validated_data['created_by'] = Auth::user()->getAuthIdentifier();
            $this->model->fill($validated_data);
            if ($this->model->save()) {
                return $this->created([
                    'message' => TemplatesController::TEMPLATE_CREATED,
                    'data' => $this->model->toArray()
                ]);
            }
            return $this->failed(['message' => TemplatesController::TEMPLATE_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show(Request $request, $id, $to, $cc_id)
    {
        try {
            $auto_generated_url_id = null;
            $mail_template = $this->model::where('id', $id)->first();

            //Check If template has "#DYNAMIC:RecruiterZoom#" wildcard
            if (stripos($mail_template->body, "#DYNAMIC:RecruiterZoom#") !== false) {
                if(is_null(Auth::user()->zoom_personal_link)){
                    $user_service = new UserService();
                    $response = $user_service->getZoomPersonalLink(Auth::user()->id);
                }
            }
            switch ($to){
                case 'candidate': {
                    $mail_template->body = $this->getCompiledCandidateTemplate($mail_template, $cc_id);
                    break;
                }
                case 'candidate-profile': {
                    $mail_template->body = $this->getCompiledCandidateProfileTemplate($mail_template, $cc_id);
                    break;
                }
                case 'company': {
                    $mail_template->body = $this->getCompiledCompanyTemplate($mail_template, $cc_id);
                    break;
                }
                case 'company-job': {
                    $mail_template->body = $this->getCompiledCompanyJobTemplate($mail_template, $cc_id, $auto_generated_url_id);
                    break;
                }
                case 'verify-job': {
                    $mail_template->body = $this->getCompiledCompanyVerifyJobTemplate($mail_template, $cc_id, $auto_generated_url_id);
                    break;
                }
                default: {
                    throw new \Exception('Broken url');
                }
            }

            if (is_null($mail_template)) {
                return $this->noRecord(['message' => TemplatesController::RECORD_NOT_FOUND]);
            } else {
                $mail_template->subject = $mail_template->title;
                $response = ['template' => $mail_template];
                if(!is_null($auto_generated_url_id)){
                    $response['auto_generated_url_id'] = $auto_generated_url_id;
                }
                return $this->created($response);
            }

        } catch (ZoomIDNotFound $ex) {
            Log::error($ex);
            return response()->json(['message' => $ex->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ZoomAccountUnverified $ex) {
            Log::error($ex);
            return response()->json(['message' => $ex->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    private function getCompiledCompanyTemplate(&$mail_template, $company_id){
        $company_pr = CompanyPr::where('id', $company_id)->with(['company', 'companyContacts'])->first();
        if(is_null($company_pr)){
            throw new \Exception('Company Not Found');
        }
        $to_emails = $company_pr->companyContacts->pluck('email')->toArray();

        $loggedin_user = Auth::user();
        $recruiter_name = explode(' ', $loggedin_user->user->name);
        $templateParser = new TemplateParser($mail_template->body);
        $templateParser->__set('CompanyName', $company_pr->company->first()->name);
        $templateParser->__set('RecruiterFirstName', $recruiter_name[0]);
        $templateParser->__set('RecruiterLastName', (isset($recruiter_name[1])) ? $recruiter_name[1] : '');
        $templateParser->__set('RecruiterEmail', $loggedin_user->email);
        $templateParser->__set('RecruiterPhone', $loggedin_user->phone);
        $templateParser->__set('RecruiterZoom', $loggedin_user->zoom_personal_link);
        $templateParser->__set('Signature', $loggedin_user->user->name);
        $templateParser->process();
        $mail_template->to = implode(',', $to_emails);
        return $templateParser->getCompiled();
    }

    private function getCompiledCompanyJobTemplate(&$mail_template, $company_id, &$auto_generated_url_id){
        $company_pr = CompanyPr::where('id', $company_id)->with(['company', 'companyContacts'])->first();
        if(is_null($company_pr)){
            throw new \Exception('Company Not Found');
        }

        $loggedin_user = Auth::user();
        $recruiter_name = explode(' ', $loggedin_user->user->name);
        $loggedin_user_id = Auth::id();
        $token = Str::orderedUuid()->toString();
        $auto_generated_url_id = $company_pr->autoGenerateURL()->create(['status' => 1, 'token' => $token, 'user_pr_id' => $loggedin_user_id])->id;


        $to_emails = $company_pr->companyContacts->pluck('email')->toArray();

        $templateParser = new TemplateParser($mail_template->body);
        $templateParser->__set('CompanyName', $company_pr->company->first()->name);
        $templateParser->__set('PostJobURL', '<a href="'. URL::temporarySignedRoute('company.job.create', now()->addDays(env('COMPANY_JOB_URL_EXPIRY', 2)), ['company_pr_id' => Crypt::encryptString($company_id), 'token' => $token]) .'">LINK</a>');
        $templateParser->__set('RecruiterFirstName', $recruiter_name[0]);
        $templateParser->__set('RecruiterLastName', (isset($recruiter_name[1])) ? $recruiter_name[1] : '');
        $templateParser->__set('RecruiterEmail', $loggedin_user->email);
        $templateParser->__set('RecruiterPhone', $loggedin_user->phone);
        $templateParser->__set('RecruiterZoom', $loggedin_user->zoom_personal_link);
        $templateParser->__set('Signature', $loggedin_user->user->name);
        $templateParser->process();
        $mail_template->to = implode(',', $to_emails);
        return $templateParser->getCompiled();
    }

    private function getCompiledCompanyVerifyJobTemplate(&$mail_template, $job_id, &$auto_generated_url_id){
        $job_pr = JobPr::where('id', $job_id)->first();
        if(is_null($job_pr)){
            throw new \Exception('Job Not Found');
        }

        $loggedin_user = Auth::user();
        $recruiter_name = explode(' ', $loggedin_user->user->name);
        $loggedin_user_id = Auth::id();
        $token = Str::orderedUuid()->toString();
        $auto_generated_url_id = $job_pr->autoGenerateURL()->create(['status' => 1, 'token' => $token, 'user_pr_id' => $loggedin_user_id])->id;

        $templateParser = new TemplateParser($mail_template->body);
        $templateParser->__set('CompanyName', $job_pr->companyPr->company->first()->name);
        $templateParser->__set('VerifyJobURL', '<a href="'. URL::temporarySignedRoute('company.job.verify', now()->addDays(env('COMPANY_JOB_URL_EXPIRY', 2)), ['job_pr_id' => Crypt::encryptString($job_id), 'token' => $token]) .'">LINK</a>');
        $templateParser->__set('RecruiterFirstName', $recruiter_name[0]);
        $templateParser->__set('RecruiterLastName', (isset($recruiter_name[1])) ? $recruiter_name[1] : '');
        $templateParser->__set('RecruiterEmail', $loggedin_user->email);
        $templateParser->__set('RecruiterPhone', $loggedin_user->phone);
        $templateParser->__set('RecruiterZoom', $loggedin_user->zoom_personal_link);
        $templateParser->__set('Signature', $loggedin_user->user->name);
        $templateParser->process();
        $mail_template->to = '';
        return $templateParser->getCompiled();
    }

    private function getCompiledCandidateTemplate(&$mail_template, $candidate_id){
        $candidate_pr = CandidateCV::where('id', $candidate_id)->with(['recruiter'])->first();
        if(is_null($candidate_pr)){
            throw new \Exception('Candidate Not Found');
        }

        $recruiter = User::where('id', $candidate_pr->recruiter->user_ID)->first();
        if(is_null($recruiter)){
            throw new \Exception('Recruiter Not Found');
        }

        $recruiter_name = explode(' ', $recruiter->name);

        $templateParser = new TemplateParser($mail_template->body);
        $templateParser->__set('CandidateFirstName', $candidate_pr->first_name);
        $templateParser->__set('CandidateLastName', $candidate_pr->last_name);
        $templateParser->__set('CandidateEmail', $candidate_pr->email);
        $templateParser->__set('CandidatePhone', $candidate_pr->phone);
        $templateParser->__set('RecruiterFirstName', $recruiter_name[0]);
        $templateParser->__set('RecruiterLastName', (isset($recruiter_name[1])) ? $recruiter_name[1] : '');
        $templateParser->__set('RecruiterEmail', $recruiter->user_pr->email);
        $templateParser->__set('RecruiterPhone', $recruiter->user_pr->phone);
        $templateParser->__set('RecruiterZoom', $recruiter->user_pr->zoom_personal_link);
        $templateParser->__set('Signature', $recruiter->name);
        $templateParser->process();
        $mail_template->to = $candidate_pr->email;
        return $templateParser->getCompiled();
    }

    private function getCompiledCandidateProfileTemplate(&$mail_template, $candidate_id){
        $candidate_pr = CandidateCV::where('id', $candidate_id)->with(['recruiter'])->first();
        if(is_null($candidate_pr)){
            throw new \Exception('Candidate Not Found');
        }

        $recruiter = User::where('id', $candidate_pr->recruiter->user_ID)->first();
        if(is_null($recruiter)){
            throw new \Exception('Recruiter Not Found');
        }

        $loggedin_user_id = Auth::id();
        $token = Str::orderedUuid()->toString();
        $candidate_pr->autoGenerateURL()->create(['status' => 1, 'token' => $token, 'user_pr_id' => $loggedin_user_id]);

        $recruiter_name = explode(' ', $recruiter->name);

        $templateParser = new TemplateParser($mail_template->body);
        $templateParser->__set('CandidateFirstName', $candidate_pr->first_name);
        $templateParser->__set('CandidateLastName', $candidate_pr->last_name);
        $templateParser->__set('CandidateEmail', $candidate_pr->email);
        $templateParser->__set('CandidatePhone', $candidate_pr->phone);
        $templateParser->__set('RecruiterFirstName', $recruiter_name[0]);
        $templateParser->__set('RecruiterLastName', (isset($recruiter_name[1])) ? $recruiter_name[1] : '');
        $templateParser->__set('RecruiterEmail', $recruiter->user_pr->email);
        $templateParser->__set('RecruiterPhone', $recruiter->user_pr->phone);
        $templateParser->__set('RecruiterZoom', $recruiter->user_pr->zoom_personal_link);
        $templateParser->__set('CandidateProfileURL', '<a href="'. URL::temporarySignedRoute('candidate.cv.profile', now()->addDays(env('CANDIDATE_PROFILE_URL_EXPIRY', 2)), ['candidatecv_id' => Crypt::encryptString($candidate_id), 'token' => $token]).'">LINK</a>');
        $templateParser->__set('Signature', $recruiter->name);
        $templateParser->process();
        $mail_template->to = $candidate_pr->email;
        return $templateParser->getCompiled();
    }

    public function update(UpdateTemplateRequest $request)
    {
        try {
            $updatedTemplate = $this->model::where('id', $request->id)->update($request->except('id'));
            if ($updatedTemplate) {
                return $this->created([
                    'message' => TemplatesController::TEMPLATE_UPDATED,
                    'data' => $this->model->where('id', $request->id)->first()
                ]);
            }
            return $this->failed(['message' => TemplatesController::TEMPLATE_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function delete(UpdateTemplateRequest $request)
    {
        try {
            $updatedTemplate = $this->model::where('id', $request->id)->update($request->except('id'));
            if ($updatedTemplate) {
                return $this->created([
                    'message' => TemplatesController::TEMPLATE_UPDATED,
                    'data' => $this->model->where('id', $request->id)->first()
                ]);
            }
            return $this->failed(['message' => TemplatesController::TEMPLATE_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteTemplateRequest $request)
    {
        try {
            $template_ids = $request->get('id');

            DB::transaction(function () use ($template_ids) {
                foreach ($template_ids as $template_id){
                    $this->findOneById($template_id)->delete();
                }
            });

            return $this->created(['message' => TemplatesController::TEMPLATE_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

