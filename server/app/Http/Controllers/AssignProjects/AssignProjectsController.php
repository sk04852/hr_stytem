<?php

namespace App\Http\Controllers\AssignProjects;

use App\Http\Controllers\AssignEmployees\Models\AssignEmployee;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AssignProjects\Models\AssignProject as ThisModel;
use App\Http\Controllers\AssignProjects\Requests\UpdateAssignProjectsRequest as UpdateRequest;
use App\Http\Controllers\AssignProjects\Requests\DeleteAssignProjectsRequest as DeleteRequest;
use App\Http\Controllers\AssignProjects\Requests\AssignProjectsToTeamRequest as AssignProjectRequest;
use App\Http\Controllers\Generics\Models\Team;
use App\Http\Controllers\Project\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignProjectsController extends Controller
{
    const MODULE_NAME = 'assign_projects';
    const COLLECTION_NAME = 'assign_projects';
    const PORJECT_ASSIGNED = 'Project has been assigned to team';
    const UPDATE_FAILED = 'Failed to update the assign project';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $records = $this->retrieveRecords();
            return $this->created([AssignProjectsController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['company_id'] = $this->companyId();
            $data = $this->model->create($data);
            if($data) {
                return $this->created(['message'=> AssignProjectsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->model->find($id);
            if( $record ){
                return $this->created([AssignProjectsController::MODULE_NAME => $record]);
            }
                return $this->noRecord(['message' => AssignProjectsController::RECORD_NOT_FOUND]); 
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
                return $this->created(['message'=> AssignProjectsController::RECORD_UPDATED]);
            }
            $this->failed(['message'=>AssignProjectsController::UPDATE_FAILED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteRequest $request)
    {
        try {
            $id = $request->id;
            $assignProject = $this->model->find($id);
            if($assignProject) {
               $assignProject->delete();
               return $this->created(['message'=> AssignProjectsController::RECORD_DELETED]);
            }
            return $this->noRecord(['message' => AssignProjectsController::RECORD_NOT_FOUND]); 
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function assignProjectToTeams(AssignProjectRequest $request) {
        try { 
            $project = Project::where('id', $request->project_id)->first();
            $team = Team::select('name')->whereIn('id', $request->team_id)->get();
            $teamIds = Team::select('id')->whereIn('id', $request->team_id)->get();
            $assignEmployees = AssignEmployee::whereIn('team_id', $request->team_id)->get();
            $employeesStatus = $assignEmployees->pluck('status');

            $membersInTeam = DB::table('assign_employees as ae')
                        ->leftJoin('employees','employees.id','ae.employee_id')
                        ->leftJoin('users as u', 'employees.user_id', 'u.id')
                        ->leftJoin('generic_teams as teams', 'teams.id', 'ae.team_id')
                        ->whereIn('ae.team_id', $teamIds)
                        ->get([
                        'ae.id', 'u.first_name', 'u.last_name', 'u.email', 'ae.status', 'teams.id as team_id', 'ae.employee_id as employee_id'
                        ]);

             $result = array (
                         "Project Name" => $project->project_name,
                         "Team" => $team,
                         "Memebers In Team" => $membersInTeam
                     );

            return $this->created(['message'=>AssignProjectsController::PORJECT_ASSIGNED, $result]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function assignProjectToEmployees(array $status) {

    }
}

    //     foreach ($employeesStatus as $key => $values) {
        //                                                         //checks availability of employees before assigning project
        //           if ($employeesStatus[$key] == "active") {
        //         $team->update(['param_1' => $project->id,
        //                       'name' => $team[$key]->name]);
        //          }
        //             else
        //                 $this->assignProjectToEmployees($status);
        //    }

   // $membersInTeam = DB::table('assign_employees as ae')            // Undo this if array $result is remeoved
            //                 ->join('employees','ae.employee_id', 'employees.id')
            //                 ->join('users as u', 'employees.user_id', 'u.id')
            //                 ->join('generic_teams as teams', 'ae.team_id', 'teams.id')
            //                 ->join('assign_projects as ap', 'teams.id', 'ap.team_id')
            //                 ->join('projects', 'ap.project_id', 'projects.id')
            //                 ->whereIn('ae.employee_id', $employeesIds)
            //                 ->get([
            //                 'ae.id as Assign Employee ID: ', 
            //                 'projects.project_name as Project Name',
            //                 'teams.name as Team: ', 
            //                 'u.first_name as First Name', 
            //                 'u.last_name as Last Name', 
            //                 'u.email as Email', 
            //                 'ae.status as Status', 
            //                 ]);