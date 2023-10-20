<?php

namespace App\Http\Controllers\AssignAsset;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AssignAsset\Models\AssignAsset as ThisModel;
use App\Http\Controllers\AssignAsset\Requests\CreateAssignAssetRequest as CreateRequest;
use Illuminate\Http\Request;

class AssignAssetsController extends Controller
{
    const MODULE_NAME = 'assign_asset';
    const COLLECTION_NAME = 'assign_assets';
    const ASSET_ALREADY_ASSIGNED = 'This asset already assigned.';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
                $assets =  $this->model->listing($this->companyId())
                ->when(!empty($request->asset_name), function ($query) use ($request) {
                    return $query->where('asset_name', $request->asset_name);
                })
                ->when(!empty($request->first_name), function ($query) use ($request) {
                    return $query->where('first_name', 'like', '%' . $request->first_name . '%');
                })
                ->when(!empty($request->last_name), function ($query) use ($request) {
                    return $query->where('last_name', 'like', '%' . $request->last_name . '%');
                })->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());

                if($assets->isNotEmpty()){
                    return $this->created([AssignAssetsController::COLLECTION_NAME => $assets]);
                }
                return $this->noRecord(['message' => AssignAssetsController::RECORD_NOT_FOUND,'data' => $assets],200);  

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    
    public function store(CreateRequest $request)
    {
        try {
            $data = $request->all();
            $checkAssigned = $this->model->checkAlreadyAssigned($data, $this->companyId());
            if ($checkAssigned) {
                return $this->created(['message' => AssignAssetsController::ASSET_ALREADY_ASSIGNED]);
            }
                $this->model->UpdateAssetStatus($data, $this->companyId());
                $data['company_id'] = $this->companyId();
                $this->model->create($data);
                return $this->created(['message'=> AssignAssetsController::RECORD_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
   
    public function show($id)
    {
        try {
              $record = $this->model->find($id);
             if ($record) {
                $asset =  $this->model->listing($this->companyId())
                ->where('aa.id', $id)->first();
                 return $this->created([AssignAssetsController::MODULE_NAME => $asset]);
             }
             return $this->noRecord(['message' => AssignAssetsController::RECORD_NOT_FOUND],200); 
         } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $data = $this->model->find($id);
            if($data) {
               $data->delete();
                return $this->created(['message'=> AssignAssetsController::RECORD_DELETED]);
            }
            return $this->noRecord(['message' => AssignAssetsController::RECORD_NOT_FOUND],200); 
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
