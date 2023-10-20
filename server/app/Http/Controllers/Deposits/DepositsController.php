<?php namespace App\Http\Controllers\Deposits;

use App\Http\Controllers\Controller;
use Exception;
use App\Http\Controllers\Clients\ClientsController;
use App\Http\Controllers\Clients\Models\Client;
use Illuminate\Http\Response;
use App\Http\Controllers\Deposits\Models\Deposit;
use App\Http\Controllers\Deposits\Requests\DepositRequest;
use App\Http\Controllers\Clients\Commissions;
use App\Notifications\NewAccountDeposit;
use GuzzleHttp\Client as GuzzleClient;

class DepositTypes{
    const INVESTMENT = 'INVESTMENT';
    const SUBSCRIPTION_FEE = 'SUBSCRIPTION_FEE';
}

class DepositsController extends Controller{

    private $clientsController;
    private $commissions_;

    public function __construct(Deposit $model, ClientsController $clientsController, Commissions $commissions){
        $this->model_ = $model;
        $this->clientsController = $clientsController;
        $this->commissions_ = $commissions;
        parent::__construct();
    }

    public function save(DepositRequest $request){
        try{
            $auth = $this->app["user"];
            $client = $this->clientsController->findById($auth->id)->first();
            $depositData = $request->all();
            $depositData["client_id"] = $client->id;
            $deposit = new Deposit($depositData);
            $deposit->bonus = 0;
            $deposit->total = $deposit->bonus + $deposit->amount;
            if($deposit->save()){
                if($this->clientsController->creditAccount($client, $deposit->amount)){
                    $this->commissions_->payCommissionToReferrer($client, $deposit);
                    return response()->json(["message"=>"Deposit made successfully", "deposit"=>$deposit],Response::HTTP_OK);
                }else
                    return response()->json(["message"=>"Deposit was not saved successfully"],Response::HTTP_BAD_REQUEST);
            }else{
                return response()->json(["error"=>"Deposit failed, please try again."],Response::HTTP_BAD_REQUEST);
            }
        }catch(Exception $ex){
            return response()->json(["error"=>$ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createDeposit(int $clientId, string $methodId, float $amount, float $bonus=0.00, $isSubscriptionFee = false){
        try{
            $client = $this->clientsController->findById($clientId)->first();
            $deposit = new Deposit();
            $deposit->type = ($isSubscriptionFee)? DepositTypes::SUBSCRIPTION_FEE : DepositTypes::INVESTMENT;
            $deposit->client_id = $clientId;
            $deposit->method_id = $methodId;
            $deposit->amount = $amount;
            $deposit->bonus = $bonus;
            $deposit->total = $deposit->bonus + $deposit->amount;
            if($deposit->save()){
                if ($isSubscriptionFee) {
                    $client->openSubscription();
                    return response()->json(["message"=>"Subscription fee made successfully", "deposit"=>$deposit],Response::HTTP_OK);
                } else {
                    if($this->clientsController->creditAccount($client, $deposit->amount)){
                        $this->commissions_->payCommissionToReferrer($client, $deposit);
                        return response()->json(["message"=>"Deposit made successfully", "deposit"=>$deposit],Response::HTTP_OK);
                    }else
                        return response()->json(["message"=>"Deposit was not saved successfully"],Response::HTTP_BAD_REQUEST);
                }
            }else{
                return response()->json(["error"=>"Deposit failed, please try again."],Response::HTTP_BAD_REQUEST);
            }
        }catch(Exception $ex){
            return response()->json(["error"=>$ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createBinaryDeposit(int $clientId, float $amount, string $paymentId, string $token = ''){
        try{
            $client = $this->clientsController->findById($clientId)->first();
            $token = (!empty($token))?
                    $token : $this->clientsController->createBinaryLoginToken($client->email);

            $GuzzleClient = new GuzzleClient([
                'headers' => [
                    'Authorization'=>'Bearer '.$token,
                ],
                'allow_redirects' => false
            ]);
            $tokenEndpoint = env("BINARY_PLATFORM_ENDPOINT")."/deposits";
            $response = $GuzzleClient->post($tokenEndpoint, [
                \GuzzleHttp\RequestOptions::JSON => [
                    'amount' => $amount,
                    'reference' => 'Perfect Money Deposit ('.$paymentId.')'
                ]
            ]);
            $NotificationData = [
                            'full_name'=>$client->getFullName(),
                            'psp'=>'Perfect Money',
                            'amount'=>$amount
                        ];
            // To-Do
            // Merge both notification into one call.
            $client->notify(new NewAccountDeposit($NotificationData)); // Send a slack notification
            //$this->clientsController->EmailClient($client, 'account_deposited', $EmailData);
            return json_decode($response->getBody()->getContents());
        } catch(Exception $ex) {

        }
    }

    public function isFTD(Client $client){
        $noOfDeposits = Deposit::where('client_id',$client->id)->count();
        return $noOfDeposits == 1? true: false;
    }

    public function getDeposits(){
        try {
            $auth = $this->app["user"];
            $deposits = Deposit::where('client_id',$auth->id)->orderBy("id","DESC")->get();
            return response()->json(["deposits"=>$deposits],Response::HTTP_OK);
        } catch(Exception $ex) {
            return response()->json(["error"=>$ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getClientsController(){
        return $this->clientsController;
    }

    public function processSubscriptionFee(int $clientId, string $methodId, float $amount){

    }
}

?>
