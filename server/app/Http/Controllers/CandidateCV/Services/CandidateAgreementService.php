<?php
/**
 * CandidateAgreementService
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\CandidateCV\Services;

use App\Events\Timeline\CandidateCV\CandidateAgreementCreated;
use App\Events\Timeline\CandidateCV\CandidateAgreementDeleted;
use App\Http\Controllers\Agrello\Models\AgrelloResponse;
use App\Http\Controllers\Agrello\Services\AgrelloService;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\CandidateCV\Models\CandidateCVAgreement;
use App\Http\Controllers\CandidateCV\Models\CandidateCVAgreementFile;
use App\Http\Controllers\CandidateCV\Requests\CreateAgreementRequest;
use App\Http\Controllers\Timelines\Models\Timeline;
use App\Http\Controllers\Timelines\Traits\TimelineTrait;
use App\Models\UserPr;
use App\Notifications\Agrello\DocumentSignatureAdded;
use App\Notifications\Agrello\DocumentSigned;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\FileHelpers;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CandidateAgreementService
{
    use TimelineTrait;

    const EMAIL_PERSONAL_CODE_ERROR = 'Candidate must have email';
    const CANDIDATE_AGREEMENT_UPLOAD_ERROR = 'Candidate agreement uploaded files not found';
    const CANDIDATE_AGREEMENT_INVALID_FORMAT = 'Agreement invalid upload files. only (docx, doc, pdf, txt) allowed';
    const CANDIDATE_AGREEMENT_UPLOAD_DISK = 'candidatecv';
    const CANDIDATE_AGREEMENT_UPLOAD_PATH = 'uploads/candidatecv/agrrements';

    const LANGUAGE_PATH = 'app.Http.Controllers.CandidateCV.Services.CandidateAgreementService';

    public $agrello_service;

    public function __construct(AgrelloService $agrello_service)
    {
        $this->agrello_service = $agrello_service;
    }

    public function createAgreement(CreateAgreementRequest $request)
    {
        $response = null;
        DB::transaction(function () use ($request, &$response) {
            $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
            if (!$request->hasFile('files')) {
                throw new Exception(self::CANDIDATE_AGREEMENT_UPLOAD_ERROR, 422);
            }

            $candidatecv = CandidateCV::find($request->candidatecv_id);
            if(is_null($candidatecv)){
                throw new ModelNotFoundException('Candidate not found');
            }

            if(is_null($candidatecv->email)){
                throw new UnprocessableEntityHttpException(self::EMAIL_PERSONAL_CODE_ERROR);
            }

            $user_pr_object = UserPr::find($request->user_pr_id);
            if(is_null($user_pr_object)){
                throw new ModelNotFoundException('Recruiter not found');
            }

            $agreement_data['candidatecv_id'] = $candidatecv->id;
            $agreement_data['user_pr_id'] = $user_pr_object->id;
            $agreement_data['agreement_status_id'] = $request->user_pr_id;

            $agreement_object =  CandidateCVAgreement::create($agreement_data);

            $allowedfileExtension = ['docx', 'doc', 'txt', 'pdf', 'PDF'];
            $files = $request->file('files');
            $errors = [];

            foreach ($files as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                $check = in_array($extension, $allowedfileExtension);

                if ($check) {
                    $file_data['path'] = $file->store(self::CANDIDATE_AGREEMENT_UPLOAD_PATH, self::CANDIDATE_AGREEMENT_UPLOAD_DISK);
                    $file_data['file_name'] = $file->getClientOriginalName();
                    $agreement_object->files()->create($file_data);
                    $this->recordOneToManyNew('agreement', $file_data['file_name'], $timeline_data);

                    $created_template_response = $this->agrello_service->createTemplate($file);
                    AgrelloResponse::create([
                        'candidatecv_id' => $candidatecv->id,
                        'user_pr_id' => $request->user_pr_id,
                        'event' => AgrelloResponse::CREATE_TEMPLATE_MULTIPART_CONTENT,
                        'event_id' => $created_template_response->id,
                        'response' => json_encode($created_template_response),
                        'additional_data' => json_encode(['file_name' => $file_data['file_name']]),
                        'status' => AgrelloResponse::CREATED
                    ]);

                    $signers_email = [];
                    array_push($signers_email, $user_pr_object->email);
                    array_push($signers_email, $candidatecv->email);

                    $created_container_response = $this->agrello_service->createContainerFromTemplate($created_template_response->id, $file_data['file_name'], $signers_email);
                    AgrelloResponse::create([
                        'candidatecv_id' => $candidatecv->id,
                        'user_pr_id' => $request->user_pr_id,
                        'event' => AgrelloResponse::CREATE_CONTAINER_BY_TEMPLATE_REQUEST,
                        'event_id' => $created_container_response->id,
                        'response' => json_encode($created_container_response),
                        'additional_data' => json_encode(['file_name' => $file_data['file_name']]),
                        'status' => AgrelloResponse::WAITING
                    ]);

                } else {
                    throw new Exception(self::CANDIDATE_AGREEMENT_INVALID_FORMAT, 422);
                }
            }

            CandidateAgreementCreated::dispatch($candidatecv, 'Candidate Agreement Created', $timeline_data['old_values'], $timeline_data['new_values']);
            $response = $agreement_object;
        });

        return $response;
    }

    public function documentSignatureAdded($agrello_event_data){
        $agrello_container_data = AgrelloResponse::where('event_id', '=', $agrello_event_data['event']['containerId'])
            ->where('event', '=', AgrelloResponse::CREATE_CONTAINER_BY_TEMPLATE_REQUEST)
            ->where('status', '=', AgrelloResponse::WAITING)
            ->with('userPr.user', 'candidatecv')
            ->first();

        if(isset($agrello_event_data['event']['username']) && $agrello_event_data['event']['username'] == $agrello_container_data->candidatecv->email){
            Notification::send($agrello_container_data->userPr, new DocumentSignatureAdded([
                'title' => sprintf(__('notifications.agrello.document_signature_added'), $agrello_container_data->candidatecv->full_name),
                'image' => $agrello_container_data->candidatecv->photo
            ]));
        }
    }

    public function documentSigned($agrello_event_data){
        DB::transaction(function() use ($agrello_event_data){
            $agrello_container_data = AgrelloResponse::where('event_id', '=', $agrello_event_data['event']['containerId'])
                ->where('event', '=', AgrelloResponse::CREATE_CONTAINER_BY_TEMPLATE_REQUEST)
                ->where('status', '=', AgrelloResponse::WAITING)
                ->with('userPr.user', 'candidatecv.agreement.files')
                ->first();

            $file = $agrello_container_data->candidatecv->agreement->files->first();
            if(is_null($file)){
                throw new Exception(__(self::LANGUAGE_PATH.'.agreement_file_missing'));
            }
            $pathinfo = pathinfo($file->file_name);
            $file_name = $pathinfo['filename'].'.'.strtolower(AgrelloResponse::ASICE);
            $file_path = self::CANDIDATE_AGREEMENT_UPLOAD_PATH."/".Str::random(40).'.'.$pathinfo['extension'];

            $downloaded_container = $this->agrello_service->downloadContainer($agrello_event_data['event']['containerId']);
            if (!Storage::disk(self::CANDIDATE_AGREEMENT_UPLOAD_DISK)->put($file_path, $downloaded_container)) {
                throw new Exception(__(self::LANGUAGE_PATH.'.download_container_exception'));
            }

            $agrello_container_data->candidatecv->agreement->files()->create([
                'file_name' => $file_name,
                'path' => $file_path
            ]);

            $agrello_container_data->status = AgrelloResponse::DOWNLOADED;
            $agrello_container_data->save();


            $agrello_template_data = AgrelloResponse::where('candidatecv_id', '=', $agrello_container_data->candidatecv->id)
                ->where('user_pr_id', '=', $agrello_container_data->userPr->id)
                ->where('event', '=', AgrelloResponse::CREATE_TEMPLATE_MULTIPART_CONTENT)
                ->where('status', '=', AgrelloResponse::CREATED)
                ->first();

            if(is_null($agrello_template_data)){
                throw new Exception(__(self::LANGUAGE_PATH.'.agrello_template_not_found'));
            }

            $this->agrello_service->deleteTemplate($agrello_template_data->event_id);

            $agrello_container_data->candidatecv->agreement->agreement_status_id = 2;
            $agrello_container_data->candidatecv->agreement->save();

            Notification::send($agrello_container_data->userPr, new DocumentSigned([
                    'title' => sprintf(__('notifications.agrello.document_signed'), $agrello_container_data->candidatecv->full_name),
                    'image' => $agrello_container_data->candidatecv->photo
            ]));

            $file->delete();
            $agrello_template_data->delete();
            $agrello_container_data->delete();

        });
    }

    public function deleteCandidateAgreement($candidate_agreement_id){
        $response = false;
        DB::transaction(function () use ($candidate_agreement_id, &$response){
            $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
            $agreement_object = CandidateCVAgreement::find($candidate_agreement_id);
            $candidatecv = $agreement_object->candidatecv;
            if(is_null($agreement_object)){
                throw new ModelNotFoundException('Agreement not found');
            }
            $files = $agreement_object->files;
            if($files->isNotEmpty()){
                foreach ($files as $file){
                    $this->recordOneToManyDelete($file, 'agreement', 'file_name', $timeline_data);
                    $file->delete();
                }
            }

            $response = $agreement_object->delete();
            CandidateAgreementDeleted::dispatch($candidatecv, 'Candidate Agreement Deleted', $timeline_data['old_values'], $timeline_data['new_values']);
        });

        return $response;
    }

}
