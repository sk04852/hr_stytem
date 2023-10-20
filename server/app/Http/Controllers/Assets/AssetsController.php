<?php

namespace App\Http\Controllers\Assets;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Assets\Models\Asset as ThisModel;
use App\Http\Controllers\Assets\Requests\CreateAssetRequest as CreateRequest;
use App\Http\Controllers\Assets\Requests\UpdateAssetRequest as UpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetsController extends Controller
{
    const MODULE_NAME = 'asset';
    const COLLECTION_NAME = 'assets';
    const RECORD_ALREADY_EXIST = 'Record against this serial id already exists';


    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $assets = $this->model->assetFilters($request)->withAsset()->withLocation()->forThisCompany($this->companyId())
                                  ->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if($assets->isNotEmpty()){
                return $this->created([AssetsController::COLLECTION_NAME => $assets]);
            }
                return $this->noRecord(['message' => AssetsController::RECORD_NOT_FOUND, 'assets' => $assets],200); 
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    // public function store(CreateRequest $request)
    // {
    //     try {
    //         $data = $request->all();
    //         $loggedInCompany = $this->companyId();
    //          $recordExists = DB::table('assets')->where('serial_number',$request->serial_number)->where('company_id',$loggedInCompany)->exists();
    //         if ($recordExists) {
    //             return $this->noRecord(['message' => AssetsController::RECORD_ALREADY_EXIST],200); 
    //         }
    //         $data['company_id'] = $loggedInCompany;
    //         $data = $this->model->create($data);
    //             return $this->created(['message'=> AssetsController::RECORD_CREATED]);
    //     } catch (Exception $ex) {
    //         return $this->serverError($ex);
    //     }
    // }

    public function store(CreateRequest $request)
    {
        try {
            $data = $request->all();
            $data['company_id'] = $this->companyId();
            $data = $this->model->create($data);
            if($data) {
                return $this->created(['message'=> AssetsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->model->withAsset()->withLocation()->find($id);
            if( $record ){
                return $this->created([AssetsController::MODULE_NAME => $record]);
            }
                return $this->noRecord(['message' => AssetsController::RECORD_NOT_FOUND],200); 
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $data = $request->all();
            $record = $this->model->find($data['id'])->update($data);
            if($record) {
                return $this->created(['message'=> AssetsController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $record = $this->model->find($id);
            if ($record) {
                $record->delete();
                return $this->created(['message'=> AssetsController::RECORD_DELETED]);
            }
                return $this->created(['message'=> AssetsController::RECORD_NOT_FOUND],200);
            
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
