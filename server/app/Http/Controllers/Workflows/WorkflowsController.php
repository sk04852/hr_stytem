<?php
namespace App\Http\Controllers\Workflows;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Workflows\Models\Workflow as ThisModel;
use App\Http\Controllers\Workflows\Models\WorkflowTodo;
use App\Http\Controllers\Workflows\Requests\CreateWorkflowRequest as CreateRequest;
use App\Http\Controllers\Workflows\Requests\UpdateWorkflowRequest as UpdateRequest;
use App\Http\Controllers\Workflows\Requests\DeleteWorkflowRequest as DeleteRequest;
use App\Http\Services\UserService;
use App\Http\Services\WorkflowTodoDataService;
use Illuminate\Http\Request;

class WorkflowsController extends Controller
{
    private $workflowTodoDataService_;
    private $userService_;

    const MODULE_NAME = 'workflow';
    const COLLECTION_NAME = 'workflows';

    public function __construct(ThisModel $model,
                                WorkflowTodoDataService $workflowTodoDataService,
                                UserService $userService)
    {
        parent::__construct($model);
        $this->workflowTodoDataService_ = $workflowTodoDataService;
        $this->userService_ = $userService;
    }
    public function index(Request $request)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'workflows.index'))
                return $this->notAllowed(["message" => WorkflowsController::UNAUTHORIZED]);
            $records = $this->model->filter($request)
                                   ->forCompany($this->companyId())
                                   ->withModule()->orderBy($this->getSortBy(), $this->getSort())
                                   ->paginate($this->getPerPage());
            if($records->isEmpty()){
                return $this->noRecord(['message' => WorkflowsController::RECORD_NOT_FOUND],200);
            }
            return $this->created([WorkflowsController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'workflows.store'))
                return $this->notAllowed(["message" => WorkflowsController::UNAUTHORIZED]);
            $this->model->fill($request->all());
            $this->model->company_id = $this->companyId();
            if($this->model->save()) {
                return $this->created([WorkflowsController::MODULE_NAME => $this->model, 'message'=> WorkflowsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'workflows.show'))
                return $this->notAllowed(["message" => WorkflowsController::UNAUTHORIZED]);
            $record = $this->model->find($id);
            if(!$record){
                return $this->noRecord(['message' => WorkflowsController::RECORD_NOT_FOUND],200);
            }else{
                if(!$this->isCanBeAccessByUser($record->company_id))
                    return $this->notAllowed(["message" => WorkflowsController::UNAUTHORIZED]);
                return $this->created([WorkflowsController::MODULE_NAME => $record]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'workflows.update'))
                return $this->notAllowed(["message" => WorkflowsController::UNAUTHORIZED]);
            $record = $this->model->find($request->id);
            if(!$record)
            {
                return $this->noRecord(['message' => WorkflowsController::RECORD_NOT_FOUND],200);
            }
            if(!$this->isCanBeAccessByUser($record->company_id))
                return $this->notAllowed(["message" => WorkflowsController::UNAUTHORIZED]);
            $record->fill($request->except(['module_id','company_id']));
            if($record->save()) {
                return $this->created([WorkflowsController::MODULE_NAME => $record, 'message'=> WorkflowsController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteRequest $request)
    {
        try {
            if(!$this->userService_->isUserAllowedTo($this->userId(),'workflows.delete'))
                return $this->notAllowed(["message" => WorkflowsController::UNAUTHORIZED]);
            $record = $this->model->find($request->id);
            if(!$record)
                return $this->noRecord(['message' => WorkflowsController::RECORD_NOT_FOUND],200);
            if(!$this->isCanBeAccessByUser($record->company_id))
                return $this->notAllowed(["message" => WorkflowsController::UNAUTHORIZED]);
            $records = WorkflowTodo::where('workflow_id', $request->id)->get();
            foreach($records as $recordTodo) {
                $todo = $recordTodo->todo;
                $todoDataId = $recordTodo->data;
                if($recordTodo->delete())
                $this->workflowTodoDataService_->destroyTodoData($todo, $todoDataId);
            }
            if ($record->delete()) {
                return $this->created(['message' => WorkFlowsController::RECORD_DELETED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
