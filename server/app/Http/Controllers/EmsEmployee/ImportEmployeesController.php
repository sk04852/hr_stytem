<?php

namespace App\Http\Controllers\EmsEmployee;

use App\Http\Controllers\EmsEmployee\Models\Employee;
use App\Imports\ImportEmployee;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmsEmployee\Requests\ImportEmployeeRequest;
use App\Http\Services\UserService;

class ImportEmployeesController extends Controller
{
    const IMPORT_SUCCESS = "Data imported successfully";
    private $userService_;

    public function __construct(Employee $model, UserService $userService)
    {
        $this->model = $model;
        $this->userService_ = $userService;
    }


    public function store(ImportEmployeeRequest $request)
    {
            $file = $request->file('file')->store('import');
            dd($file);
            $import = new ImportEmployee;
            $import->company_id = $this->companyId();
            $import->import($file);

        return $this->created(['message' => ImportEmployeesController::IMPORT_SUCCESS]);
    }

}
