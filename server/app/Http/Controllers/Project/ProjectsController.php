<?php

namespace App\Http\Controllers\Project;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Project\Models\Project as Thismodel;
use App\Http\Controllers\Project\Requests\CreateProjectRequest;
use App\Http\Controllers\Project\Requests\UpdateProjectRequest;
use Exception;
use App\Http\Services\UserService;

class ProjectsController extends Controller
{
    const MODULE_NAME = 'project';
    const PROJECT_CREATED = 'New project created successfully';
    const PROJECT_NOT_CREATED = 'Error in creating a project';
    const COLLECTION_NAME = 'projects';
    const PROJECT_UPDATED = 'Project updated successfully';
    const PROJECT_NOT_UPDATED = 'Error in updating project';
    const PROJECT_DELETED = 'Project deleted successfully';
    const PROJECT_NOT_DELETED = 'Error in deleting project';
    const PROJECT_ALREADY_MARKED = 'Project already marked';
    const PROJECT_NOT_FOUND = 'Project not found';
    const INVALID_EMPLOYEE_ID = 'Project not found, invalid employee id';
    private $userService_;

    public function __construct(ThisModel $model, UserService $userService)
    {
        parent::__construct($model);
        $this->userService_ = $userService;
    }

    public function index(Request $request)
    {
        try {
            $project_name = $this->model->when(!empty($request->project_name), function ($query) use ($request) {
                return $query->where('project_name', $request->project_name);
            });
              if ($project_name) {
                  $project_name = $this->model
                ->when(!empty($request->project_name), function ($query) use ($request) {
                    return $query->where('project_name', $request->project_name);
                })
                ->when(!empty($request->link), function ($query) use ($request) {
                    return $query->where('link', $request->link);
                })
                ->when(!empty($request->status), function ($query) use ($request) {
                    return $query->where('status', $request->status);
                })
            ->where('company_id', '=', $this->userService_->getCompanyId($this->userId()))
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());
                  if ($project_name->isNotEmpty()) {
                      return $this->created([ProjectsController::COLLECTION_NAME => $project_name]);
                  }
              }
            return $this->noRecord(['message' => ProjectsController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateProjectRequest $request)
    {
        try {
            $this->model->fill($request->all());
            $this->model->company_id = $this->companyId();
            if ($this->model->save()) {
                $this->addLog('info',$this->created,$this->model,ProjectsController::MODULE_NAME,$this->type(),$this->userId());
                return $this->created(['message' => ProjectsController::PROJECT_CREATED]);
            }
            return $this->failed(['message' => ProjectsController::PROJECT_NOT_CREATED]);
        } catch (Exception $ex) {
            $this->addLog('Error',$this->created,$ex->getMessage(),ProjectsController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }

    public function update(UpdateProjectRequest $request)
    {
        try {
            $updatedProject = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedProject) {
                $this->addLog('info',$this->updated,$updatedProject,ProjectsController::MODULE_NAME,$this->type(),$this->userId());
                return $this->created(['message' => ProjectsController::PROJECT_UPDATED]);
            }
            return $this->failed(['message' => ProjectsController::PROJECT_NOT_UPDATED]);
        } catch (Exception $ex) {
            $this->addLog('Error',$this->updated,$ex->getMessage(),ProjectsController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }

    public function destroy(UpdateProjectRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            $this->addLog('info',$this->delete,$request->id,ProjectsController::MODULE_NAME,$this->type(),$this->userId());
            return $this->created(['message' => ProjectsController::PROJECT_DELETED]);
        } catch (Exception $ex) {
            $this->addLog('Error',$this->deleted,$ex->getMessage(),ProjectsController::MODULE_NAME,'System','0');
            return $this->serverError($ex);
        }
    }
    }

