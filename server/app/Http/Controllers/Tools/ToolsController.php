<?php

namespace App\Http\Controllers\Tools;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\Models\Tool as ThisModel;
use App\Http\Controllers\Tools\Requests\CreateToolRequest as CreateRequest;
use App\Http\Controllers\Tools\Requests\UpdateToolRequest as UpdateRequest;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    const MODULE_NAME = 'tool';
    const COLLECTION_NAME = 'tools';
    const RECORD_DELETED_FAILED = 'Tool deleted Failed';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $tools = $this->model->toolsFilters($request)
                                  ->forThisCompany($this->companyId())
                                //   ->withCompany()
                                  ->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if($tools->isNotEmpty()){
              return $this->created([ToolsController::COLLECTION_NAME => $tools]);
            }
                return $this->noRecord(['message' => ToolsController::RECORD_NOT_FOUND,ToolsController::COLLECTION_NAME => $tools],200); 
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $data = $request->all();
            $data['company_id'] = $this->companyId();
            $data = $this->model->create($data);
            if($data) {
                return $this->created(['message'=> ToolsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->model->withCompany()->find($id);
            if( $record ){
                return $this->created([ToolsController::MODULE_NAME => $record]);
            }
                return $this->noRecord(['message' => ToolsController::RECORD_NOT_FOUND],200); 
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
                return $this->created(['message'=> ToolsController::RECORD_UPDATED]);
            }
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
               return $this->created(['message'=> ToolsController::RECORD_DELETED]);
            }
            return $this->noRecord(['message' => ToolsController::RECORD_NOT_FOUND],200); 
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


}
