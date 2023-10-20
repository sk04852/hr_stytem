<?php

namespace App\Http\Controllers\Payments;

use Exception;
use App\Models\Module;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Services\UserService;
use App\Events\IncomingPaymentEvent;
use App\Http\Controllers\Controller;
use App\Http\Services\ClientsService;
use App\Http\Services\CommentService;
use App\Http\Services\PaymentService;
use App\Http\Services\AccountingService;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Modules\Enums\ModuleEnum;
use App\Http\Controllers\Payments\Models\Payment as ThisModel;
use App\Http\Controllers\Modules\Enums\SaleTransactionTypeEnum;
use App\Http\Controllers\Payments\Requests\CreatePaymentRequest as CreateRequest;
use App\Http\Controllers\Payments\Requests\UpdatePaymentRequest  as UpdateRequest;

class PaymentsController extends Controller
{
    const MODULE_NAME = 'payment';
    const COLLECTION_NAME = 'payments';
    private $userService_;
    private $clientService_;
    private $accountingService_;
    private $paymentService_;
    private $commentService_;

    public function __construct(ThisModel $model,
                                UserService $userService,
                                AccountingService $accountingService,
                                PaymentService $paymentService,
                                CommentService $commentService,
                                ClientsService $clientService)
    {
        parent::__construct($model);
        $this->userService_ = $userService;
        $this->clientService_ = $clientService;
        $this->accountingService_ = $accountingService;
        $this->paymentService_ = $paymentService;
        $this->commentService_ = $commentService;
    }

    public function index(Request $request)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'payments.index'))
                return $this->notAllowed(["message" => PaymentsController::UNAUTHORIZED]);
            $records =  $this->model->paymentFilters($request)->with('client')->with('company')->with('outlet')
                                  ->orderBy($this->getSortBy(), $this->getSort())
                                  ->paginate($this->getPerPage());
            if($records->isNotEmpty()){
                return $this->created([PaymentsController::COLLECTION_NAME => $records]);
            }
            return $this->noRecord(['message' => PaymentsController::RECORD_NOT_FOUND, PaymentsController::COLLECTION_NAME => $records],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    public function store(CreateRequest $request)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'payments.store'))
                return $this->notAllowed(["message" => PaymentsController::UNAUTHORIZED]);
                $this->model->fill($request->all());
                $this->model['recieved_by'] = $this->userId();
                $this->model['company_id'] = $this->companyId();
                $this->model['outlet_id'] = $this->outletId();
                $notes = ($request->notes == null)? "": $request->notes;
                $clientInformation = $this->clientService_->getClientInformation($this->model['client_id']);
                // $userInformation = $this->userService_->getUserWithCompanyAndOutlet($this->userId());

            if($this->model->save()) {
                 $this->paymentService_->createTransactions($this->model,
                                                            $this->model->id,
                                                            $this->model->outlet_id,
                                                            $clientInformation,
                                                            SaleTransactionTypeEnum::ClientPayment,
                                                            $request->amount,
                                                            $notes,
                                                            $this->companyId());
                //event(new IncomingPaymentEvent($userInformation, $clientInformation, $this->model));
                $this->recordAccountingTransaction($this->model);
                $this->createPaymentComment($this->model);
                return $this->created([PaymentsController::MODULE_NAME => $this->model, 'message'=> PaymentsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
             return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request) {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'payments.update'))
                return $this->notAllowed(["message" => PaymentsController::UNAUTHORIZED]);
            $data = $request->all();
            $data['outlet_id'] = $this->outletId();
            $record = $this->model->find($request->id);
            if($record->update($data)) {
                // $clientInformation = $this->clientService_->getClientInformation($request->client_id);
                // $userInformation = User::with('company.outlets')->find($this->userId());
                $this->paymentService_->getTransactionsForPayment($record);
                $this->paymentService_->updateAccount($this->companyId(), SaleTransactionTypeEnum::ClientPayment, $request->amount);
                //event(new UpdateIncomingPaymentEvent($userInformation, $clientInformation, $record));
                return $this->created(['message'=> PaymentsController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(int $id) {
        if(!$this->userService_->isUserAllowedTo($this->userId(),'payments.update'))
            return $this->notAllowed(["message" => PaymentsController::UNAUTHORIZED]);
         $record = $this->model->find($id);
         $record['outlet_id'] = $this->outletId();
        if ($record) {
            if ($record->delete()) {
                $transaction = $this->paymentService_->getTransactionsForPayment($record);
                $this->paymentService_->updateAccountsAfterDeletion($transaction, $this->companyId());
                return $this->created(['message'=> PaymentsController::RECORD_DELETED]);
           }
        } else {
            return $this->noRecord(['message'=> PaymentsController::RECORD_NOT_FOUND]);
        }
    }

    private function recordAccountingTransaction(ThisModel $payment) {
        $paymentModule = Module::find(getModuleIdFromEntity($payment));
        $client = $payment->client;
        $outstandingReceivables = 0;
        $unusedCredits = 0;
        $outstandingReceivables = $client->outstanding_receivables;

        if($outstandingReceivables >= $payment->amount) {
            $remainingRecveivables = $outstandingReceivables - $payment->amount;
            $payment->client->update(['outstanding_receivables'=> $remainingRecveivables]);
        } else {
            $unusedCredits = $payment->amount - $outstandingReceivables;
            $totalUnusedCredits = $unusedCredits + $client->unused_credits;
            $payment->client->update(['outstanding_receivables'=> 0, 'unused_credits'=> $totalUnusedCredits]);
        }
        $this->accountingService_->record(
            $this->companyId(),
            SaleTransactionTypeEnum::ClientPayment,
            $paymentModule,
            $payment,
            $this->paymentService_->getPaymentTransactionId($payment->company_id, $payment->outlet_id, $payment->id),
            $payment->amount,
            "Payment of amount ". $payment->amount. " via ". $payment->payment_type
        );
    }

    private function createPaymentComment(ThisModel $payment) {
        $comment = "Payment of ". $payment->client->currency. $payment->amount. " received by <strong>".$this->user()->first_name. " ". $this->user()->last_name."</strong>";
        $commentId = Str::uuid();
        $this->commentService_->createComment($comment, $this->userId(), false, ModuleEnum::Clients, $payment->client->id, $commentId);
    }
}
