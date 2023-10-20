<?php
/**
 * AgrelloService
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\Agrello\Services;

use App\Http\Controllers\Agrello\Models\AgrelloResponse;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Client;

class AgrelloService
{
    protected $configurations;

    public function __construct()
    {
        $this->configurations = json_decode(file_get_contents(storage_path('app/agrello/agrello-new.json')), true);
    }

    public function createAuthUrl()
    {

        $url_parameters = sprintf("?response_type=code&client_id=%s&redirect_uri=%s",
            $this->configurations['app']['client_id'],
            $this->configurations['app']['redirect_uris'][0],
        );

        return $this->configurations['app']['auth_uri'] . $url_parameters;
    }

    public function getAccessToken($authorization_code)
    {
        $scopes = 'createContainerUploadingFiles';
        $redirectUri = $this->configurations['app']['redirect_uris'][0];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://service.agrello.org/auth/oauth/token?code=$authorization_code&scopes=$scopes&redirect_uri=$redirectUri",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=authorization_code',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode($this->configurations['app']['client_id'] . ':' . $this->configurations['app']['client_secret']),
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function refreshToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.agrello.io/public/v2/auth/accessToken',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => '{
              "clientId": "' . $this->configurations['app']['client_id'] . '",
              "clientSecret": "' . $this->configurations['app']['client_secret'] . '",
              "refreshToken": "' . $this->configurations['app']['refresh_token'] . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'Content-Type: application/json'
            ),
        ));

        $response = json_decode(curl_exec($curl));

        curl_close($curl);

        if (isset($response->error)) {
            throw new \Exception(isset($response->error_description) ? $response->error_description : $response->error);
        }

        if (isset($response->access_token) && isset($response->refresh_token)) {
            $this->rewriteRefreshToken($response->access_token, $response->refresh_token);
        }

        return $response;
    }

    public function rewriteRefreshToken($access_token, $refresh_token)
    {
        $this->configurations['app']['access_token'] = $access_token;
        $this->configurations['app']['refresh_token'] = $refresh_token;
        File::put(storage_path('app/agrello/agrello-new.json'), json_encode($this->configurations));
    }

    public function createTemplate($file)
    {
        $this->refreshToken();

        $client = new Client();
        $headers = [
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->configurations['app']['access_token']
        ];
        $options = [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => Utils::tryFopen($file->path(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ]
                ],
                [
                    'name' => 'request',
                    'contents' => '{"clientId": ' . $this->configurations['app']['client_id'] . '}'
                ]
            ]];
        $request = new Request('POST', 'https://api.agrello.io/public/v2/templates', $headers);
        $res = $client->sendAsync($request, $options)->wait();
        return json_decode($res->getBody()->getContents());
    }

    public function createContainerFromTemplate($template_id, $file_name, $signers_email)
    {
        $this->refreshToken();

        $client = new Client();
        $headers = [
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->configurations['app']['access_token'],
            'Content-Type' => 'application/json'
        ];

        $body_data = [
           'clientId' =>  $this->configurations['app']['client_id'],
            'immediatePublish' => true,
            'name' => $file_name,
            'outputType' => AgrelloResponse::ASICE,
            'signers' => $signers_email,
            'templateId' => $template_id,
            'useExtraPayment' => false,
            'variables' => [],
            'viewers' => []
        ];

        $body = json_encode($body_data);
        $request = new Request('POST', 'https://api.agrello.io/public/v2/containers/file', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody()->getContents());

    }

    public function downloadContainer($container_id){
        $this->refreshToken();

        $client = new Client();
        $headers = [
            'accept' => 'application/octet-stream',
            'Authorization' => 'Bearer '. $this->configurations['app']['access_token']
        ];
        $request = new Request('GET', "https://api.agrello.io/public/v2/containers/$container_id/output", $headers);
        $res = $client->sendAsync($request)->wait();
        return $res->getBody()->getContents();
    }

    public function deleteTemplate($template_id){
        $this->refreshToken();

        $client = new Client();
        $headers = [
            'accept' => '*/*',
            'Authorization' => 'Bearer '. $this->configurations['app']['access_token']
        ];
        $request = new Request('DELETE', "https://api.agrello.io/public/v2/templates/$template_id", $headers);
        $res = $client->sendAsync($request)->wait();
        echo $res->getBody()->getContents();
    }

}
