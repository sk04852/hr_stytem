<?php

namespace App\Http\Controllers\SMTPConnections;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SMTPConnections\Models\SMTPConnection as ThisModel;
use App\Http\Controllers\SMTPConnections\Requests\CreateSMTPConnectionsRequest as CreateRequest;
use App\Http\Controllers\SMTPConnections\Requests\UpdateSMTPConnectionsRequest as UpdateRequest;
use App\Http\Controllers\SMTPConnections\Requests\UpdateMassSMTPConnectionsRequest as MassUpdateRequest;
use App\Http\Controllers\SMTPConnections\Requests\DeleteSMTPConnectionsRequest as DeleteRequest;
use App\Http\Controllers\SMTPConnections\Requests\DeleteMassSMTPConnectionsRequest as MassDeleteRequest;
use App\Http\Controllers\SMTPConnections\Requests\SendMailSMTPConnectionsRequest as SendMailRequest;
use App\Http\Services\UserService;
use App\Mail\AdminEmailServerDeactivationEmail;
use App\Mail\SMTPEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Swift_SmtpTransport as SmtpTransport;
use Swift_TransportException;

class SMTPConnectionsController extends Controller
{
    const MODULE_NAME = 'smtp_connection';
    const COLLECTION_NAME = 'smtp_connections';
    const SERVER_DEACTIVATED = 'Authentication failed';
    const MAIL_SEND = 'Email has been send successfully';

    private $userService_;

    public function __construct(ThisModel $model, UserService $userService )
    {
        parent::__construct($model);
        $this->userService_ = $userService;
    }

    public function index(Request $request)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'smtpConnections.index'))
                return $this->notAllowed(["message" => SMTPConnectionsController::UNAUTHORIZED]);
            $records = $this->model->forCompany($this->companyId())
                                    ->when(!empty($request->server_name), function ($query) use ($request) {
                                        return $query->where('server_name', $request->server_name);
                                    })
                                    ->when(!empty($request->port), function ($query) use ($request) {
                                        return $query->where('port', $request->port);
                                    })
                                    ->when(!empty($request->user_name), function ($query) use ($request) {
                                        return $query->where('user_name', $request->user_name);
                                    })
                                    ->when(!empty($request->requires_authentication), function ($query) use ($request) {
                                        return $query->where('requires_authentication', $request->requires_authentication);
                                    })
                                    ->orderBy($this->getSortBy(), $this->getSort())
                                    ->paginate($this->getPerPage());
            if ($records->isNotEmpty()) {
                return $this->created([SMTPConnectionsController::COLLECTION_NAME => $records]);
            }
            return $this->created([SMTPConnectionsController::COLLECTION_NAME => []]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'smtpConnections.store'))
                return $this->notAllowed(["message" => SMTPConnectionsController::UNAUTHORIZED]);
            $this->model->fill($request->all());
            $this->model->company_id = $this->companyId();
            if($this->model->save()) {
                return $this->created([SMTPConnectionsController::MODULE_NAME => $this->model, 'message'=> SMTPConnectionsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'smtpConnections.show'))
                return $this->notAllowed(["message" => SMTPConnectionsController::UNAUTHORIZED]);
                $record = $this->model->where('id', $id)->first();
            if (!$record) {
                return $this->noRecord(['message' => SMTPConnectionsController::RECORD_NOT_FOUND]);
            } else {
                if(!$this->isCanBeAccessByUser($record->company_id))
                    return $this->notAllowed(["message" => SMTPConnectionsController::UNAUTHORIZED]);
                return $this->created([SMTPConnectionsController::MODULE_NAME => $record]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'smtpConnections.update'))
                return $this->notAllowed(["message" => SMTPConnectionsController::UNAUTHORIZED]);
                $record = $this->model->where('id', $request->id)->first();
            if (!$record) {
                return $this->noRecord(['message' => SMTPConnectionsController::RECORD_NOT_FOUND]);
            } else {
                if(!$this->isCanBeAccessByUser($record->company_id))
                    return $this->notAllowed(["message" => SMTPConnectionsController::UNAUTHORIZED]);
                $record->fill($request->all());
                if($record->save())
                    return $this->created(['message' => SMTPConnectionsController::RECORD_UPDATED]);
                return $this->failed(['message' => SMTPConnectionsController::RECORD_NOT_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteRequest $request)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'smtpConnections.delete'))
                return $this->notAllowed(["message" => SMTPConnectionsController::UNAUTHORIZED]);
            $record = $this->model->where('id', $request->id)->first();
            if (!$record) {
                return $this->noRecord(['message' => SMTPConnectionsController::RECORD_NOT_FOUND]);
            } else {
                if(!$this->isCanBeAccessByUser($record->company_id))
                    return $this->notAllowed(["message" => SMTPConnectionsController::UNAUTHORIZED]);
                if ($record->delete()) {
                    return $this->created(['message'=> SMTPConnectionsController::RECORD_DELETED]);
                }
                return $this->failed(['message'=> SMTPConnectionsController::RECORD_NOT_DELETED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    public function massDelete(MassDeleteRequest $request) {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'smtpConnections.delete'))
                return $this->notAllowed(["message" => SMTPConnectionsController::UNAUTHORIZED]);
            $data = $request->all();
            $ids = $data['id'];
            $this->model->whereIn('id',$ids)->forCompany($this->companyId())->delete();
            return $this->created(['message'=>SMTPConnectionsController::MASS_RECORDS_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massUpdate(MassUpdateRequest $request) {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'smtpConnections.update'))
                return $this->notAllowed(["message" => SMTPConnectionsController::UNAUTHORIZED]);
            $allData = $request->all();
            $smtpIds = $allData['id'];
            $updateData = $request->except(['id']);
            $records = $this->model->whereIn('id',$smtpIds)->forCompany($this->companyId())->get();
            foreach ($smtpIds as $key => $value) {              
                $records->each(function($record) use($key,$value, $updateData) {
                    if($record->id == $value){
                        if($updateData['server_name'][$key]){
                            $record->server_name = $updateData['server_name'][$key];
                        }
                        if($updateData['port'][$key]){
                            $record->port = $updateData['port'][$key];
                        }
                        if($updateData['user_name'][$key]){
                            $record->user_name = $updateData['user_name'][$key];
                        }
                        if($updateData['mail_host'][$key]){
                            $record->mail_host = $updateData['mail_host'][$key];
                        }
                        if($updateData['password'][$key]){
                            $record->password = $updateData['password'][$key];
                        }
                        if($updateData['from_email'][$key]){
                            $record->from_email = $updateData['from_email'][$key];
                        }
                        if($updateData['requires_authentication'][$key]){
                            $record->requires_authentication = $updateData['requires_authentication'][$key];
                        }
                       $record->save();
                    }
                });
            }
            return $this->created([$this->model->whereIn('id',$smtpIds)->forCompany($this->companyId())->get(), 'message'=> SMTPConnectionsController::MASS_RECORDS_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function search(string $keywords)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'smtpConnections.index'))
                return $this->notAllowed(["message" => SMTPConnectionsController::UNAUTHORIZED]);
            $calendarEntries = $this->model->simpleSearch(['server_name','user_name','from_email'], $keywords)
                                            ->forCompany($this->companyId())
                                            ->orderBy($this->getSortBy(), $this->getSort())
                                            ->paginate($this->getPerPage());
            if (!$calendarEntries->isEmpty()) {
                return $this->created([SMTPConnectionsController::COLLECTION_NAME => $calendarEntries]);
            } else {
                return $this->noRecord(['messsage'=> SMTPConnectionsController::RECORD_NOT_FOUND]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function sendTestMail(SendMailRequest $request) {
        $response = $this->authenticate($request->id);
        if($response == null) {
            $data = [
                'message' => 'This is a test Mail'
            ];
            Mail::to($request->to_email)->send(new SMTPEmail($data));
            return response()->json(['message' => SMTPConnectionsController::MAIL_SEND]);
        } else {
            return $response;
        }
    }

    public function authenticate(int $id) {
        $connectionData = $this->model::find($id);
        if($connectionData->requires_authentication == 1) {
            $encrypyion = "ssl";
        } else {
            $encrypyion = "tls";
        }
        $config = [
                    "host"=> $connectionData->server_name,
                    "port" => $connectionData->port,
                    "username"=> $connectionData->user_name,
                    "encryption"=> $encrypyion,
                    "password"=>$connectionData->password
                ];
        $response =  $this->testSMTPAutentication($config);
        if($response == null) {
            $connectionData->status = "Active";
            $connectionData->save();
            return null;
        } else {
            $connectionData->status = "Deactivated";
            $connectionData->save();
            $data = [
                'message' => 'Your Email Server is Deactivated.',
                'error' => $response
            ];
            Mail::to('info@digibits.xyz')->send(new AdminEmailServerDeactivationEmail($data));
            return $this->failed(['messsage'=> SMTPConnectionsController::SERVER_DEACTIVATED,'error'=> $response]);
        }
    }
 
    public function testSMTPAutentication(array $config) {
        try {
            $transport = new SmtpTransport($config['host'], $config['port']);
            if (! empty($config['encryption'])) {
                $transport->setEncryption($config['encryption']);
            }
            if (isset($config['username'])) {
                $transport->setUsername($config['username']);
                $transport->setPassword($config['password']);
            }
            $mailer = new \Swift_Mailer($transport);
            $mailer->getTransport()->start();
            return null;
        } catch (Swift_TransportException $e) {
            return $e->getMessage();
        } catch (Exception $e) {
          return $e->getMessage();
        }
    }
}
