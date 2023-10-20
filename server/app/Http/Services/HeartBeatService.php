<?php
namespace App\Http\Services;

use App\Http\Controllers\HeartBeats\Models\HeartBeat;
use App\Http\Controllers\HeartBeats\Models\HeartBeatLog;
use App\Mail\HeartBeatEmail;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Mail;

class HeartBeatService {
    private $model_;
    private $tempToken_ = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3RcL0VNU1wvZW1zLWJhY2tlbmRcL3B1YmxpY1wvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYxMDQ0NzMzNiwiZXhwIjoxNjEwNDgzMzM2LCJuYmYiOjE2MTA0NDczMzYsImp0aSI6IkQxMlUxcVJOQVhHb1lhQ3oiLCJzdWIiOjEsInBydiI6IjgzN2Y4NTgzN2VkMWFmNjBlNjY5MzBkOWU4ZWFhMWY5ODNjYTA4YTMifQ.qbk9qDFLqLefLZTJbihImJzwZ0bO_TGFwsZ1Ji5xERc";
    public function __construct(HeartBeat $model) {
        $this->model_ = $model;
    }

    public function checkHeartBeat(int $heartBeatId) {
        $heartBeat = $this->model_->find($heartBeatId);
        $this->check($heartBeat, $heartBeatId);
    }

    public function check($heartBeat, int $heartBeatId) {
        $client = new Client();
        $body = [];
        if(!empty($heartBeat->variables)) {
            $heartBeatVariables = explode(',', $heartBeat->variables);
            foreach($heartBeatVariables as $heartBeatVariable) {
                $heartBeatVariableData = explode('=', $heartBeatVariable);
                $body[$heartBeatVariableData[0]] = $heartBeatVariableData[1];
            }
        }
        try {
            $response = $client->request($heartBeat->method, $heartBeat->url, [
                'headers' =>[
                    'Authorization' => 'Bearer '.$this->tempToken_,
                    'Accept' => 'application/json',
                ],
                'form_params' =>$body
                ]);
                $status = $response->getStatusCode();
                $message = 'Data found!';
            }catch(ClientException $ce){
                $status = $ce->getResponse()->getStatusCode();
                $message = $ce->getMessage();
            }catch(RequestException $re){
                $status = $re->getResponse()->getStatusCode();
                $message = $re->getMessage();
            }
        $checkedCondition = 1;
        if(!empty($heartBeat->last_response) && $heartBeat->last_response != $status ){
            $checkedCondition = 1;
        }
        elseif(!empty($heartBeat->last_checked_at)){
            $lastCheckedTime = Carbon::createFromFormat('Y-m-d H:i:s', $heartBeat->last_checked_at);
            $checkedCondition = ($lastCheckedTime->addSeconds($heartBeat->execute_after_error) <= Carbon::now());
        }
        if ($status != 200 && $checkedCondition) {
            $heartBeatLog = new HeartBeatLog();
            $heartBeatLog->url_id = $heartBeatId;
            $heartBeatLog->error_code = $status;
            $heartBeatLog->response = $message;
            $heartBeatLog->save();
            $this->heartBeatDataUpdateAfterError($heartBeat, $heartBeatLog);
        }
        elseif($status == 200 && $heartBeat->last_response != 200) {
            $heartBeat->last_checked_at = Carbon::now();
            $heartBeat->last_response = $status;
            $heartBeat->admin_notified = null;
            $heartBeat->save();
        }
    }

    public function heartBeatDataUpdateAfterError($heartBeat, $heartBeatLog) {
        $heartBeat->last_checked_at = $heartBeatLog->created_at;
        $heartBeat->last_response = $heartBeatLog->error_code;
        if($heartBeat->notification_method == "Email") {
            $notificationEmails = explode(',', $heartBeat->notification_emails);
            foreach($notificationEmails as $notificationEmail) {
                $re = Mail::to($notificationEmail)->send(new HeartBeatEmail($heartBeatLog->toArray()));
            }
            if($re == null)
            $heartBeat->admin_notified = 1;
        }
        $heartBeat->save();
    }
    
    public function heartBeatCheckAll() {
        $allHeartBeats = $this->model_->all();
        foreach($allHeartBeats as $heartBeat) {
            if(!empty($heartBeat->last_checked_at)) {
                $lastCheckedTime = Carbon::createFromFormat('Y-m-d H:i:s', $heartBeat->last_checked_at);
                $checkedCondition = ($lastCheckedTime->addSeconds($heartBeat->execute_after) <= Carbon::now());
            }
            else {
                $checkedCondition = 1;
            }
            if($checkedCondition) {
                $this->check($heartBeat, $heartBeat->id);
            }
        }
    }
}

?>