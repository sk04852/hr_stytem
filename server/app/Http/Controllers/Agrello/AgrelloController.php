<?php
/**
 * AgrelloController
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\Agrello;

use App\Http\Controllers\Agrello\Services\AgrelloService;
use App\Http\Controllers\CandidateCV\Services\CandidateAgreementService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Exception;

class AgrelloController extends BaseController
{
    protected $configurations;

    public function __construct()
    {
        $this->configurations = json_decode(file_get_contents(storage_path('app/agrello/agrello-new.json')), true);
    }


    public function oauth(AgrelloService $agrello_Service)
    {
        $redirect_url = action('Agrello\AgrelloController@oauth');
        if (!isset($_GET['code'])) {
            $auth_url = $agrello_Service->createAuthUrl();
            $filtered_url = filter_var($auth_url, FILTER_SANITIZE_URL);
            return redirect($filtered_url);
        } else {
            $response = json_decode($agrello_Service->getAccessToken($_GET['code']));
            if (isset($response->error)) {
                return response((isset($response->error_description)) ? $response->error_description : $response->error, 401);
            }

            if (isset($response->access_token) && isset($response->refresh_token)) {
                $agrello_Service->rewriteRefreshToken($response->refresh_token);
            }
        }

    }

    public function webhook(Request $request, CandidateAgreementService $candidate_agreement_service)
    {
        try {
//            $event = json_decode(@file_get_contents('php://input'));
            $event = json_decode($request->getContent(), true);

            if(isset($event['type']) && isset($event['event']['containerId'])){
                if($event['type'] == 'DOCUMENT_SIGNATURE_ADDED'){
                    $candidate_agreement_service->documentSignatureAdded($event);
                    return response('', 204);
                }

                if($event['type'] == 'DOCUMENT_SIGNED'){
                    $candidate_agreement_service->documentSigned($event);
                    return response('', 204);
                }
            }

            return response('', 204);

        } catch (Exception $ex) {
            Log::error($ex);
        }
    }
}
