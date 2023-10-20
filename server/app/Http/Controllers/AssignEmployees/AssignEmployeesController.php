<?php

namespace App\Http\Controllers\AssignEmployees;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AssignEmployees\Models\AssignEmployee as ThisModel;
use App\Http\Controllers\AssignEmployees\Requests\AssignEmployeeToTeamRequest as AssignToTeam;
use App\Http\Controllers\AssignEmployees\Requests\UpdateAssignEmployeeRequest as UpdateRequest;
use App\Http\Controllers\AssignEmployees\Requests\DeleteAssignEmployeeRequest as DeleteRequest;

class AssignEmployeesController extends Controller
{
    const MODULE_NAME = 'assign_employee';
    const COLLECTION_NAME = 'assign_employees';
    const ASSINGED_TO_TEAM = 'Employees have been assigend successfully';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        try {
            $records = $this->retrieveRecords();
            return $this->created([AssignEmployeesController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->model->find($id);
            if ($record) {
                return $this->created([AssignEmployeesController::MODULE_NAME => $record]);
            }
            return $this->noRecord(['message' => AssignEmployeesController::RECORD_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $data = $request->all();
            $record = $this->model->find($data['id'])->update($data);
            if ($record) {
                return $this->created(['message'=> AssignEmployeesController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteRequest $request)
    {
        try {
            $id = $request->id;
            $assignEmployees = $this->model->find($id);
            if ($assignEmployees) {
                $assignEmployees->delete();
                return $this->created(['message'=> AssignEmployeesController::RECORD_DELETED]);
            }
            return $this->noRecord(['message' => AssignEmployeesController::RECORD_NOT_FOUND], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function assignEmployeeToTeam(AssignToTeam $request)
    {
        try {
            $record = [];
            $id = $request->employee_id;
            foreach ($id as $key => $values) {
                $record[$key] = $this->model->updateOrCreate([
                    'employee_id'=> $request->employee_id[$key],
                    'team_id'=> $request->team_id[$key],
                    'status'=> $request->status[$key],
            ]);
            }
            if ($record) {
                return $this->created(['message'=> AssignEmployeesController::ASSINGED_TO_TEAM]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

    

