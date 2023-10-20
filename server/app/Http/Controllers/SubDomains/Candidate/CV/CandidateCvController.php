<?php
/**
 * Job
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\SubDomains\Candidate\CV;

use App\Events\InvalidateUrl;
use App\Http\Controllers\Actions\Models\Action;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\CandidateCV\Services\CandidateCVService;
use App\Http\Controllers\Nationalities\Models\Nationality;
use App\Models\AutoGenerateURL;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EducationDegrees\Models\EducationLevel;

class CandidateCvController extends Controller
{
    public function __construct(CandidateCV $model)
    {
        parent::__construct($model);
    }

    public function profile(Request $request, CandidateCVService $candidatecv_service, $candidatecv_id, $token)
    {
        try {
            $decrypted_candidatecv_id = Crypt::decryptString($candidatecv_id);

            $candidatecv = $candidatecv_service->getFullCandidateCV($decrypted_candidatecv_id);
            if (is_null($candidatecv)) {
                return response()->json(
                    [
                        'message' => 'Record Not Found'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = [];
            $data['candidatecv_id'] = $candidatecv_id;
            $data['token'] = $token;
            $data['candidatecv'] = $candidatecv;
            $data['actions'] = Action::all();
            $data['nationalities'] = Nationality::all();
            $data['education_levels'] = EducationLevel::all();

            return view('sub-domains/candidate/cv/profile', $data);
        } catch (DecryptException $exception) {
            return response()->json(
                [
                    'error' => $exception->getMessage(),
                    'ex' => $exception,
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );

        } catch (Exception $exception) {
            return response()->json(
                [
                    'error' => $exception->getMessage(),
                    'ex' => $exception,
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

    }

    public function confirm(Request $request, CandidateCVService $candidatecv_service, $candidatecv_id, $token)
    {
        try {
            $decrypted_candidatecv_id = Crypt::decryptString($candidatecv_id);
            $response = $candidatecv_service->updateStatus($decrypted_candidatecv_id, $token, CandidateCVService::STATUS_ACTIVE);

            if($response){
                return $this->created(['message' => 'Status is Successfully Updated', 'response' => $response]);
            }else{
                return $this->failed(['error' => 'Unable to Update Status']);
            }

        } catch (ModelNotFoundException $exception) {
            return $this->noRecord(['error' => $exception->getMessage()]);

        } catch (DecryptException $exception) {
            return response()->json(
                [
                    'error' => $exception->getMessage(),
                    'ex' => $exception,
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );

        } catch (Exception $exception) {
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
