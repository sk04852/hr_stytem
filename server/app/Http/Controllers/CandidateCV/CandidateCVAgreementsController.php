<?php

namespace App\Http\Controllers\CandidateCV;

use App\Http\Controllers\CandidateCV\Models\CandidateCVAgreement;
use App\Http\Controllers\CandidateCV\Models\CandidateCVAgreementFile;
use App\Http\Controllers\CandidateCV\Requests\CreateAgreementRequest;
use App\Http\Controllers\CandidateCV\Requests\CreateFileRequest;
use App\Http\Controllers\CandidateCV\Requests\DeleteAgreementRequest;
use App\Http\Controllers\CandidateCV\Requests\DeleteFileRequest;
use App\Http\Controllers\CandidateCV\Services\CandidateAgreementService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CandidateCVAgreementsController extends Controller
{
    const MODULE_NAME = 'CandidateCV';
    const CANDIDATE_AGREEMENT_CREATED = 'Candidate agreement added successfully';
    const CANDIDATE_AGREEMENT_NOT_CREATED = 'Error in adding a Candidate agreement';
    const COLLECTION_NAME = 'CandidateCVAgreements';
    const CANDIDATE_AGREEMENT_UPDATED = 'Candidate agreement updated successfully';
    const CANDIDATE_AGREEMENT_NOT_UPDATED = 'Error in updating Candidate agreement';
    const CANDIDATE_AGREEMENT_DELETED = 'Candidate agreement deleted successfully';
    const CANDIDATE_AGREEMENT_NOT_DELETED = 'Error in deleting Candidate agreement';
    const CANDIDATE_AGREEMENT_ALREADY_MARKED = 'Candidate agreement already marked';
    const CANDIDATE_AGREEMENT_NOT_FOUND = 'Candidate agreement not found';


    public function __construct(CandidateCVAgreement $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request, $id)
    {
        try {
            $agreements = $this->model->where('candidatecv_id', $id)->with(['agreementStatus', 'files'])->get();
            if ($agreements->isNotEmpty()) {
                return $this->created([CandidateCVAgreementsController::COLLECTION_NAME => $agreements->toArray()]);
            }else{
                return $this->noRecord(['message' => CandidateCVAgreementsController::RECORD_NOT_FOUND], 200);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }

    public function store(CreateAgreementRequest $request, CandidateAgreementService $candidate_agreement_service)
    {
        try {

            $response =  $candidate_agreement_service->createAgreement($request);


            if($response instanceof CandidateCVAgreement){
                return $this->created([
                    'message' => CandidateCVAgreementsController::CANDIDATE_AGREEMENT_CREATED,
                    'data' => $response->toArray()
                ]);
            }

            return $this->failed(['message' => CandidateCVAgreementsController::CANDIDATE_AGREEMENT_NOT_CREATED]);

        } catch (UnprocessableEntityHttpException $ex) {
            return $this->failed(['message' => $ex->getMessage()], 422);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteAgreementRequest $request, CandidateAgreementService $candidate_agreement_service)
    {

        try {
            $response =  $candidate_agreement_service->deleteCandidateAgreement($request->id);
            if($response){
                return $this->created([
                    'message' => CandidateCVAgreementsController::CANDIDATE_AGREEMENT_DELETED
                ]);
            }

            return $this->failed(['message' => CandidateCVAgreementsController::CANDIDATE_AGREEMENT_NOT_DELETED]);

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function downloadAgreement($id)
    {
        try {
            $agreement_file = CandidateCVAgreementFile::find($id);
            if(is_null($agreement_file)){
                throw new Exception('Candidate Agreement Record not found');
            }

            if (Storage::disk(CandidateAgreementService::CANDIDATE_AGREEMENT_UPLOAD_DISK)->exists($agreement_file->getRawOriginal('path'))) {
                $pathinfo = pathinfo(Storage::url($agreement_file->getRawOriginal('path'), PATHINFO_EXTENSION));
                return response()->download('storage/' . $agreement_file->getRawOriginal('path'), $agreement_file->file_name);

            }else{
                throw new Exception('Candidate Agreement File not found');
            }

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

