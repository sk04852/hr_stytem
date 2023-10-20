<?php
namespace App\Http\Services;

use App\Http\Controllers\Companies\Models\Company;
use App\Http\Controllers\EmsEmployee\Models\Employee;

class CompaniesService {

    private $employeesService_;
    private $model_;
    public function __construct(
        Company $model,
        UserService $userService,
        EmployeesService $employeesService) {
        $this->employeesService_ = $employeesService;
        $this->userService_ = $userService;
        $this->model_ = $model;
    }

    public function getEmployees(Company $company) {
        return $this->employeesService_->getEmployees(['company_id'=> $company->id]);
    }

    public function getEmployeesByCompanyId(int $companyId) {

        return $this->employeesService_->getEmployees(['company_id'=> $companyId]);
    }

    public function getCompanyEmployeesByUserId(int $companyId) {
        return $this->employeesService_->getEmployeesByCompanyId($companyId);
    }

    public function generateSubDomain(string $companyName, int $increment = 0) {
        $subDomain = str_replace([" ", ".","-"], "", $companyName);

        if($increment !=0) {
            $subDomain = $subDomain .'-'. $increment;
        }

        if($this->model_->where('sub_domain', $subDomain)->exists()) {
            $increment += 1;
            return $this->generateSubDomain($subDomain, $increment);
        }
        return $subDomain;
    }

    public function createCompany(array $data) {
        $company = $this->model_->fill($data);
        $company->save();
        return $company;
    }

    public function getCompany(int $companyId, $with = ['generalSettings', 'logo.media.type']) {
        return $this->model_->where('id', $companyId)->with($with)->first();
    }
}

?>

