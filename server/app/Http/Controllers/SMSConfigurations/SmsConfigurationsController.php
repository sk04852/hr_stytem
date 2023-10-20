<?php

namespace App\Http\Controllers\SMSConfigurations;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SMSConfigurations\Models\SmsConfiguration as ThisModel;
use App\Http\Controllers\SMSConfigurations\Requests\CreateSmsConfigurationRequest as CreateRequest;
use App\Http\Controllers\SMSConfigurations\Requests\UpdateSmsConfigurationRequest as UpdateRequest;
use Illuminate\Http\Request;

class SmsConfigurationsController extends Controller
{
    const MODULE_NAME = 'sms_configuration';
    const COLLECTION_NAME = 'sms_configurations';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function changeStatus($companyId){
        $this->model->where('company_id', $companyId)->update(['status' => 'Deactivated']);
    }

    public function index(Request $request)
    {  
        try {
            $sms = $this->model->smsFilters($request)
                                  ->forThisCompany($this->companyId())
                                  ->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if($sms->isNotEmpty()){
                return $this->created([SmsConfigurationsController::COLLECTION_NAME => $sms]);
            }
                return $this->noRecord(['message' => SmsConfigurationsController::RECORD_NOT_FOUND, SmsConfigurationsController::COLLECTION_NAME => $sms],200); 
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $data = $request->all();
            if($data['status'] == 'Active'){
                $this->changeStatus($this->companyId());
            }
            $data['company_id'] = $this->companyId();
            $data = $this->model->create($data);
            if($data) {
                return $this->created(['message'=> SmsConfigurationsController::RECORD_CREATED]);
            }
                return $this->created(['message'=> SmsConfigurationsController::RECORD_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $data = $request->all();
            if($data['status'] == 'Active'){
                $this->changeStatus($this->companyId());
            }
            $record = $this->model->where('id', $data['id'])->where('company_id', $this->companyId())->update($data);
            if($record) {
                return $this->created(['message'=> SmsConfigurationsController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $fleet = $this->model->where('id', $id)->where('company_id', $this->companyId())->first();
            if($fleet) {
               $fleet->delete();
               return $this->created(['message'=> SmsConfigurationsController::RECORD_DELETED]);
            }
               return $this->noRecord(['message' => SmsConfigurationsController::RECORD_NOT_FOUND],200); 
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } 
    }

}
