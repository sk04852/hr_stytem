<?php

namespace App\Http\Controllers\BrandsSMTP;

use App\Http\Controllers\Brands\Models\Brand;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BrandsSMTP\Models\BrandSMTP as ThisModel;
use App\Http\Controllers\BrandsSMTP\Requests\CreateBrandSMTPRequest as CreateRequest;
use App\Http\Controllers\BrandsSMTP\Requests\UpdateBrandSMTPRequest as UpdateRequest;
use App\Http\Controllers\BrandsSMTP\Requests\DeleteBrandSMTPRequest as DeleteRequest;
use App\Http\Controllers\BrandsSMTP\Requests\SetDefaultBrandSMTPRequest as SetDefaultRequest;
use App\Http\Controllers\BrandsSMTP\Requests\GetDefaultBrandSMTPRequest as GetDefaultRequest;

class BrandsSMTPController extends Controller
{
    const MODULE_NAME = 'brand_smtp';
    const COLLECTION_NAME = 'brands_smtp';
    const ALREADY_EXISTS = 'This data already exists';
    const NO_UPDATE_PERMISSION = "You can't update that data";
    const NO_VIEW_PERMISSION = "You can't view that data";
    const NO_DELETE_PERMISSION = "You can't delete that data";
    const BRANDS_SMTP_SET = "Brands Default Smtp set";
    const NO_PERMISSIONS_SET = "You don't have the permission to set default id for this brand";
    const NO_DEFAULT_SMTP = "There is no default smtp for this brand";
    const NO_PERMISSIONS_GET = "You don't have the permission to get default smtp id for this brand";
    const NO_PERMISSIONS_CREATE = "You don't have the permission to create smtp id for this brand";

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        try {
            $brandIds = Brand::select("id")->where("created_by",$this->userId())->orWhere("assigned_to",$this->userId())->get();
            $records = $this->model->whereIn('brand_id',$brandIds)->get();
            return $this->created([BrandsSMTPController::COLLECTION_NAME => $records]);
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
            $exists = 0;
            $brandIds = Brand::select("id")->where("created_by",$this->userId())->orWhere("assigned_to",$this->userId())->get();
            foreach($brandIds as $brandId) {
                if($brandId->id == $request->brand_id) {
                    $exists = 1;
                    break;
                }
            }
            if($exists == 0) {
                return $this->created(['message'=> BrandsSMTPController::NO_PERMISSIONS_CREATE]); 
            }
            if($this->model->where('brand_id',$request->brand_id)->where('smtp_id',$request->smtp_id)->exists()) {
                return $this->created(['message'=> BrandsSMTPController::ALREADY_EXISTS]);
            }
            $this->model->where('brand_id', $request->brand_id)
                        ->where('default', 'Yes')
                        ->update(["default" => "No"]);
            $this->model->fill($request->all());
            if($this->model->save()) {
                return $this->created([BrandsSMTPController::MODULE_NAME => $this->model, 'message'=> BrandsSMTPController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $brandIds = Brand::select("id")->where("created_by",$this->userId())->orWhere("assigned_to",$this->userId())->get();
            $record = $this->model->whereIn('brand_id',$brandIds)->where('id',$id)->get();
            if($record) {
                return $this->created([BrandsSMTPController::MODULE_NAME => $record]);
            }
            return $this->created(['message'=> BrandsSMTPController::NO_VIEW_PERMISSION]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function edit($id)
    {
        return;
    }

    public function update(UpdateRequest $request)
    {
        try {
            if($this->model->where('brand_id',$request->brand_id)->where('smtp_id',$request->smtp_id)->where('default',$request->default)->exists()) {
                return $this->created(['message'=> BrandsSMTPController::ALREADY_EXISTS]);
            }
            if($request->default = "Yes" ) {
                $this->model->where('brand_id', $request->brand_id)
                        ->where('default', 'Yes')
                        ->update(["default" => "No"]);
            }
            $brandIds = Brand::select("id")->where("created_by",$this->userId())->orWhere("assigned_to",$this->userId())->get();
            $record = $this->model->whereIn('brand_id',$brandIds)
                            ->where('id',$request->id)
                            ->update([
                                "brand_id" => $request->brand_id,
                                "smtp_id" => $request->smtp_id,
                                "default" => $request->default,
                            ]);
            
            if($record) {
                    return $this->created([BrandsSMTPController::MODULE_NAME => $record, 'message'=> BrandsSMTPController::RECORD_UPDATED]);
            }
            return $this->created(['message'=> BrandsSMTPController::NO_UPDATE_PERMISSION]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    public function destroy(DeleteRequest $request)
    {
        try {
            $brandIds = Brand::select("id")->where("created_by",$this->userId())->orWhere("assigned_to",$this->userId())->get();
            $record = $this->model->whereIn('brand_id',$brandIds)->where('id',$request->id)->delete();
            if($record) {
                    return $this->created(['message'=> BrandsSMTPController::RECORD_DELETED]);
            }
            return $this->created(['message'=> BrandsSMTPController::NO_DELETE_PERMISSION]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function setBrandDefaultSMTP(SetDefaultRequest $request)
    {
        try {
            $exists = 0;
            $brandIds = Brand::select("id")->where("created_by",$this->userId())->orWhere("assigned_to",$this->userId())->get();
            foreach($brandIds as $brandId) {
                if($brandId->id == $request->brand_id) {
                    $exists = 1;
                    break;
                }
            }
            if($exists == 0) {
                return $this->created(['message'=> BrandsSMTPController::NO_PERMISSIONS_SET]); 
            }
            if($this->model->where('brand_id',$request->brand_id)->where('smtp_id',$request->smtp_id)->where('default', "Yes")->exists()) {
                return $this->created(['message'=> BrandsSMTPController::ALREADY_EXISTS]);
            }
            $this->model->where('brand_id', $request->brand_id)
                        ->where('default', 'Yes')
                        ->update(["default" => "No"]);
            $record = $this->model->updateOrCreate(
                [
                    "brand_id" => $request->brand_id,
                    "smtp_id" => $request->smtp_id,
                ],
                [
                    "default" => "Yes"
                ]
            );
            return $this->created([BrandsSMTPController::MODULE_NAME => $record, 'message'=> BrandsSMTPController::BRANDS_SMTP_SET]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    
    public function getBrandDefaultSMTP(GetDefaultRequest $request)
    {
        try {
            $exists = 0;
            $brandIds = Brand::select("id")->where("created_by",$this->userId())->orWhere("assigned_to",$this->userId())->get();
            foreach($brandIds as $brandId) {
                if($brandId->id == $request->brand_id) {
                    $exists = 1;
                    break;
                }
            }
            if($exists == 0) {
                return $this->created(['message'=> BrandsSMTPController::NO_PERMISSIONS_GET]); 
            }
            $record = $this->model->where('brand_id', $request->brand_id)->where('default', 'Yes')->first();
            if($record) {
                return $this->created([BrandsSMTPController::MODULE_NAME => $record]);
            }
            return $this->created(['message'=> BrandsSMTPController::NO_DEFAULT_SMTP]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
