<?php
namespace App\Http\Controllers\TimeOff;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TimeOff\Models\TimeoffType as ThisModel;
use App\Http\Controllers\TimeOff\Requests\CreateTimeOffTypeRequest as CreateRequest;
use App\Http\Controllers\TimeOff\Requests\UpdateTimeOffTypeRequest as UpdateRequest;
use App\Http\Services\UserService;

class TimeOffTypesController extends Controller
{
    const MODULE_NAME = 'timeoff_type';
    const COLLECTION_NAME = 'timeoff_types';
    private $userService_;

    public function __construct(ThisModel $model, UserService $userService)
    {
        parent::__construct($model);
        $this->userService_ = $userService;
    }

    public function index()
    {
        try {
            $records = $this->model->forThisCompany($this->companyId())
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());
            return $this->created([TimeOffTypesController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function create()
    {
        return;
    }

    public function store(CreateRequest $request)
    {
        try {
            if(!$this->userService_->belongsToGroup($this->userId(), 4)) { // Not company Owner
                return $this->notAllowed(['message' => "You are not authorized to perform this operation"]);
            } else {
                $data = $request->all();
                if (!$this->model->where('name', $data['name'])->where('company_id', '=', $this->userService_->getCompanyId($this->userId()))->exists()){
                    $companyId = $this->companyId();
                    $data['company_id'] = $companyId;
                    $this->model->fill($data);
                    if($this->model->save()) {
                        $this->addLog('info',$this->created,$data,TimeOffTypesController::MODULE_NAME,$this->type(),$this->userId());
                        return $this->created([TimeOffTypesController::MODULE_NAME => $this->model, 'message'=> TimeOffTypesController::RECORD_CREATED]);
                    }
                } else {
                    return ['message' => 'Time off type already exist'];
                }
            }
        } catch (Exception $ex) {
            $this->addLog('Error',$this->created,$ex->getMessage(),TimeOffTypesController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->findOneById($id);
            return $this->created([TimeOffTypesController::MODULE_NAME => $record]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        try {
            $auth = auth()->user();
            $record = $this->findOneById($id);
            $record->fill($request->all());
            if($record->save()) {
                $this->addLog('info',$this->updated,$record,TimeOffTypesController::MODULE_NAME,$this->type(),$this->userId());
                return $this->created([TimeOffTypesController::MODULE_NAME => $record, 'message'=> TimeOffTypesController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            $this->addLog('Error',$this->updated,$ex->getMessage(),TimeOffTypesController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        return;
    }


}
