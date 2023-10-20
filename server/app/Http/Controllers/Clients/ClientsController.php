<?php

namespace App\Http\Controllers\Clients;

use App\Events\ClientStatementEvent;
use App\Events\ForgotPasswordEvent;
use App\Events\NewClientAddedEvent;
use App\Http\Controllers\Controller;
use Exception;
use App\Http\Controllers\Clients\Requests\ClientCreateRequest as CreateRequest;
use App\Http\Controllers\Clients\Requests\AccountVerificationRequest as VerificationRequest;
use App\Http\Controllers\Clients\Requests\LoginRequest;
use App\Http\Controllers\Clients\Requests\ForgotPasswordRequest;
use App\Http\Controllers\Clients\Requests\ResetPasswordRequest;
use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Clients\Models\ClientAddress;
use App\Http\Controllers\Clients\Requests\ClientChangePasswordRequest;
use App\Http\Controllers\Clients\Requests\ResendTokenRequest;
use App\Http\Controllers\Clients\Requests\UpdateClientRequest;
use App\Http\Controllers\Clients\Requests\AddClientProductPricesRequest;
use App\Http\Controllers\Clients\Requests\UpdateClientProfileRequest;
use App\Http\Controllers\Estimates\Models\Estimate;
use App\Http\Controllers\Invoices\Models\Invoice;
use App\Http\Resources\ClientStatements\ClientStatementResource;
use App\Http\Resources\Estimates\ClientEstimates\ClientEstimatesCollection;
use App\Http\Resources\Invoices\ClientInvoices\ClientInvoicesCollection;
use App\Http\Controllers\Clients\Requests\UpdateClientAddressesRequest as UpdateClientAddressRequest;
use App\Http\Controllers\Transactions\Models\Transaction;
use App\Http\Controllers\Payments\Models\Payment;
use App\Http\Services\ClientsService;
use App\Http\Services\ClientSummeryService;
use App\Http\Services\InvoicesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientsController extends Controller
{
    const MODULE_NAME = 'client';
    const ACCOUNT_VERIFIED = 'Your account has been verified successfully';
    const VERIFICATION_CODE = 'Verification code sent at your email, please verify your account';
    const PASSWORD_RESET_CODE = 'Password reset code sent at your email';
    const PASSWORD_UPDATED = 'Password updated successfully';
    const UNAUTHORIZED = 'You are unauthorized person';
    const CLIENT_UPDATE = 'Client has been updated successfully';
    const CLIENT_UPDATE_FAILED = 'Failed to update client';
    const CLIENT_DELETE = 'Client has been deleted successfully';
    const CLIENT_ALREADY_VERIFIED = 'Client already verified';
    const CLIENT_RESEND_TOKEN = 'Token has been resent';
    const COLLECTION_NAME = 'clients';
    const PRODUCT_PRICES_SAVED = 'Product prices saved';
    const CLIENT_PAYMENTS = 'payments';
    const CLIENT_SUMMARY = 'summary';
    const CLIENT_ESTIMATES = 'estimates';
    const CLIENT_STATEMENT = 'statement';
    const CLIENT_INVOICES = 'invoices';
    const STATEMENT_SENT = 'Transactions statement sent to client successfully';

    private $clientService_;
    private $clientSummeryService_;
    private $invoicesService_;

    public function __construct(Client $model, ClientsService $clientService, ClientSummeryService $clientSummeryService, InvoicesService $invoicesService)
    {
        parent::__construct($model);
        $this->clientService_ = $clientService;
        $this->clientSummeryService_ = $clientSummeryService;
        $this->invoicesService_ = $invoicesService;



    }

    public function index(Request $request)
    {
        try {
            $clients = $this->model->clientsFilter($request)->where('company_id', $this->companyId())->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if ($clients->isNotEmpty()) {
                return $this->created([ClientsController::COLLECTION_NAME => $clients]);
            }
                return $this->noRecord(['message'=> ClientsController::RECORD_NOT_FOUND, 'clients' => $clients],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    /**
     * created_by_admin is sent from the backoffice
     * if the key is not found in the request the client will be considered as a Signup.
     * When a client signs up the status is set to Not Verified and the client is sent an email.
     * **/
    public function store(CreateRequest $request)
    {
        try {
            $isSignup = !$request->has('created_by_admin');
            $data = $request->except(['status']);
            $data['password'] = Hash::make($data['password']);
            $verificationCode = rand(1000, 9999);
            $data['code'] = $verificationCode;
            $data['company_id'] = $this->companyId();
            $data['username'] = $request->get('username', Str::upper(Str::random(6)));
            if(!$isSignup) {
                $data['status'] = 'Active'; // When created by the admin, it should be active by default.
                $data['created_by'] = $this->userId();
                unset($data['created_by_admin']);
            }
            if($client = $this->model->create($data)) {
                $shippingAddress = $request->get('shipping_address', []);
                $billingAddress = $request->get('billing_address', []);
                $billingAddress['type'] = 'Billing';
                $shippingAddress['type'] = 'Shipping';
                $client->address()->save(new ClientAddress($billingAddress)); // Billing Address
                $client->address()->save(new ClientAddress($shippingAddress)); // Billing Address
                if($isSignup) {
                    $message = ClientsController::VERIFICATION_CODE;
                    event(new NewClientAddedEvent($client, $verificationCode));
                } else {
                    $message = ClientsController::RECORD_CREATED;
                }
                return $this->created(['message'=> $message, 'client'=> $client]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function checkExistance($request){
        return $this->model->where('email',$request->email)->where('code',$request->code);
    }

    public function checkVerification($request){
       return $this->model->where('email',$request->email)->where('status','Active')->first();
    }

    public function verify(VerificationRequest $request)
    {
        try{
            $result = $this->checkExistance($request)->first();
            if($result){
                $result->update(['code' => null, 'status' => 'Active']);
                return $this->created(['message'=> ClientsController::ACCOUNT_VERIFIED]);
            }
                return $this->noRecord(['message' => ClientsController::RECORD_NOT_FOUND],200);
       }catch (Exception $ex) {
            return $this->serverError($ex);
      }
    }

    public function login(LoginRequest $request)
    {
        try{
            $client = $this->checkVerification($request);
            if(!$client){
                return $this->created(['message'=> ClientsController::UNAUTHORIZED],401);
            }else{
                if (!Hash::check($request->password, $client->password)) {
                    return $this->created(['message'=> ClientsController::UNAUTHORIZED],401);
                }else{
                    $token = sha1(time().$request->email.rand(1,1000));
                    Client::where('email', $request->email)->update(['token' => $token, 'expires_at' => Carbon::now()->addDays(1)]);
                    return response()->json(['client' => [
                                                            'first_name' => $client->first_name,
                                                            'last_name' => $client->last_name,
                                                            'username' => $client->username,
                                                            'company_name' => $client->company_name,
                                                            'email' => $client->email,
                                                            'phone' => $client->phone,
                                                            'status' => $client->status,
                                                            'outstanding_receivables' => $client->outstanding_receivables,
                                                            'status' => $client->status,
                                                            'token' => $token
                                                          ]
                    ]);
                }
            }

        }catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function clientLogout(Request $request)
    {
        try{
            $logout = Client::where('token', $request->bearerToken())->update(['token' => null, 'expires_at' => null]);
            if($logout){
                return response()->json(['message' => 'Successfully logged out']);
            }
            return response()->json(['message' => 'Not logged out, something went wrong']);
        }catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }



    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try{
            $client = $this->checkVerification($request);
            if(!$client){
                return $this->created(['message' => ClientsController::UNAUTHORIZED]);
            }
            $verificaitonCode = rand(1000, 9999);
            $this->model->where('email',$client->email)->update(['code' => $verificaitonCode]);
            event(new ForgotPasswordEvent($client, $verificaitonCode));
            return $this->created(['message'=> ClientsController::PASSWORD_RESET_CODE]);
        }catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try{
            $client = $this->checkVerification($request);
            if(!$client){
                return $this->created(['message' => ClientsController::UNAUTHORIZED]);
            }
            $found =  $this->model->where('email',$request->email)->where('code',$request->code)->first();
            if( $found ){
                $found->update(['password'=> Hash::make($request->password), 'code' => NULL]);
                return $this->created(['message'=> ClientsController::PASSWORD_UPDATED]);
            }
                return $this->created(['message'=> ClientsController::UNAUTHORIZED]);
        }catch (Exception $ex) {
                return $this->serverError($ex);
        }
    }

    public function update(UpdateClientRequest $request)
    {
        try {
            $data = $request->all();
            if(!empty($request->password)){
                 $data['password'] = Hash::make($request->password);
            }
            $client = $this->model->find($request->id)->update($data);
            if ($client) {
                $shippingAddress = $request->get('shipping_address', []);
                $billingAddress = $request->get('billing_address', []);

                if(isset($shippingAddress['id'])) {
                    ClientAddress::where('id', $shippingAddress['id'])->update($shippingAddress);
                }

                if(isset($billingAddress['id'])) {
                    ClientAddress::where('id', $billingAddress['id'])->update($billingAddress);
                }
                return $this->created(['message' => ClientsController::CLIENT_UPDATE]);
            }
                return $this->failed(['message' => ClientsController::CLIENT_UPDATE_FAILED]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(int $id)
    {
        try {
            $client = $this->model->find($id);
            if ($client) {
                $client->delete();
                return $this->created(['message' => ClientsController::CLIENT_DELETE]);
            }
                return $this->noRecord(['message' => ClientsController::RECORD_NOT_FOUND],200);

        }catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        }catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function resendToken(ResendTokenRequest $request)
    {
        try {
            $data = $request->all();
            $client = $this->model->where('email', $data['email'])->first();
            if(!$client){
                return $this->created(['message' => ClientsController::UNAUTHORIZED]);
            }
            //generate a new token here
            $verificationCode = rand(1000, 9999);
            $client->update(['code' => $verificationCode]);
            event(new ForgotPasswordEvent($client, $verificationCode));
            return $this->created(['message'=> ClientsController::VERIFICATION_CODE]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function clientsList(){
        try{
            return $this->clientService_->fetchClients($this->companyId(), -1);
        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

    public function changePassword(ClientChangePasswordRequest $request){
        $fillables = $this->model->getOnlyFillables($request->all());
        if($fillables){
            $fillables['password'] = Hash::make($request->password);
            $client = $this->model->find($request->id)->update($fillables);
            if ($client) {
               return $this->created(['message' => ClientsController::PASSWORD_UPDATED]);
            }
            return $this->failed(['message' => ClientsController::CLIENT_UPDATE_FAILED]);
        }
    }

    public function addProductPrices(AddClientProductPricesRequest $request) {
        try {
            $clientId = $request->client_id;
            $prices = $request->prices;
            $this->clientService_->addProductPrices($clientId, $prices);
            return $this->created(['message' => ClientsController::PRODUCT_PRICES_SAVED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function getProductPrices(int $clientId) {
        try {
            return $this->clientService_->getClient($clientId)->productPrices;
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    /****CLIENT FRONTEND ROUTE****/
    /****IF YOU MAKE CHANGES HERE, RETEST THE CLIENT SITE****/

    public function payments() {
        try {
            $client = getClient();
            if(is_null($client))
                throw new Exception("Invalid client");
            $payments = Payment::where('client_id', $client->id)->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            return $this->created([ClientsController::CLIENT_PAYMENTS=> $payments]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function summery() {
        try {
            $client = getClient();
            if(is_null($client))
                throw new Exception("Invalid client");
                return $this->clientSummeryService_->generateSummery($client);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function invoices() {
        try {
            $client = getClient();
            if(is_null($client))
                throw new Exception("Invalid client");
            $invoices = Invoice::where('client_id', $client->id)->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if($invoices->isNotEmpty()){
                return ClientInvoicesCollection::collection($invoices);
            }else{
                return $this->created([ClientsController::RECORD_NOT_FOUND],200);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function estimates () {
        try {
            $client = getClient();
            if(is_null($client))
                throw new Exception("Invalid client");
                $estimates = Estimate::where('client_id', $client->id)->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
                if($estimates->isNotEmpty()){
                    return ClientEstimatesCollection::collection($estimates);
                }else{
                    return $this->created([ClientsController::RECORD_NOT_FOUND],200);
                }

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function statements(Request $request)
    {
        try {
                return new ClientStatementResource($request);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function sendClientStatementViaEmail(Request $request){
        try {
            $client = getClient();
            if(is_null($client))
            throw new Exception("Invalid client");

            $transctions = Transaction::where('relation_module_id', 5)->where('relation_id', $client->id)
                        ->when(!empty($request->from), function ($query) use ($request) {
                            return  $query->whereDate('created_at', '>=', $request->from);
                        })
                        ->when(!empty($request->to), function ($query) use ($request) {
                          return  $query->whereDate('created_at', '<=', $request->to);
                        })
                        ->when(!empty($request->transaction_type), function ($query) use ($request) {
                            return  $query->where('transaction_type_id', '=', $request->transaction_type);
                        })->get();
                if($transctions->isNotEmpty()){
                    event(new ClientStatementEvent($client, $transctions, $request));
                    return $this->created(['message' => ClientsController::STATEMENT_SENT]);
                }else{
                    return $this->created(['message' => ClientsController::RECORD_NOT_FOUND],200);
                }

       } catch (Exception $ex) {
           return $this->serverError($ex);
       }
    }

    public function updateProfile(UpdateClientProfileRequest $request){
        try {
            $client = getClient();
            if(is_null($client))
                throw new Exception("Invalid client");
            $data = $request->all();
            if(!empty($request->password)){
                 $data['password'] = Hash::make($request->password);
            }
            $client = $this->model->find($client->id)->update($data);
            if ($client) {
                return $this->created(['message' => ClientsController::CLIENT_UPDATE]);
            }
                return $this->failed(['message' => ClientsController::CLIENT_UPDATE_FAILED]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function generatePDF(int $invoiceId) {
        try{
             $isGenerate = $this->invoicesService_->createPDF($invoiceId);
             if($isGenerate){
                 return $isGenerate;
             }else{
                 return $this->created(['message' => ClientsController::RECORD_NOT_FOUND],200);
             }
        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

    public function updateAddress(UpdateClientAddressRequest $request)
    {
        try {
            $shippingAddress = $request->get('shipping_address', []);
            $billingAddress = $request->get('billing_address', []);

            if(isset($shippingAddress['id'])) {
                ClientAddress::where('id', $shippingAddress['id'])->update($shippingAddress);
            }

            if(isset($billingAddress['id'])) {
                ClientAddress::where('id', $billingAddress['id'])->update($billingAddress);
            }

            return $this->created(['message' => ClientsController::RECORD_UPDATED]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


}
