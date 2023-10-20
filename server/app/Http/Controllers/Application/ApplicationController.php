<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Brands\Models\Brand;
use App\Models\Module;
use App\Models\Country;
use App\Http\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Services\CompaniesService;
use App\Http\Services\StatisticsService;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Generics\Models\Team;
use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Medias\Models\MediaType;
use App\Http\Controllers\Generics\Models\JobType;
use App\Http\Controllers\Generics\Models\Location;
use App\Http\Controllers\Generics\Models\Category;
use App\Http\Controllers\Generics\Models\Designation;
use App\Http\Controllers\Generics\Models\WorkSchedule;
use App\Http\Controllers\Options\FieldOptionsController;
use App\Http\Resources\Employees\EmployeesDigestCollection;
use App\Http\Services\RBACLService;

class ApplicationController extends Controller
{
    const MODULE_NAME = 'application';
    const COLLECTION_NAME = 'application';

    public function __construct()
    {

    }

    public function appData(User $userModel,
                            UserService $userService,
                            Category $category,
                            Module $moduleModel,
                            MediaType $mediaTypeModel,
                            FieldOptionsController $FieldOptionsController,
                            Country $countryModel,
                            Location $locationModel,
                            Designation $designationModel,
                            JobType $jobTypeModel,
                            WorkSchedule $workScheduleModel,
                            Team $teamModel,
                            CompaniesService $companiesService,
                            StatisticsService $statisticsService,
                            RBACLService $rbaclService,
                            Brand $brandModel,
                            Client $client) {

        $userId = $this->userId();
        $companyId = $this->companyId();
        $App = [];
        if($userId > 0 && $companyId > 0) {
            $App['modules'] = $moduleModel->toDigest();
            $App['users'] = $userModel->getUsersDigestForThisCompany($companyId);
            $App['countries'] = $countryModel->toDigest();
            $App['media_types'] = $mediaTypeModel->toDigest();
            $App['locations'] = $locationModel->toDigest();
            $App['field_options'] = $FieldOptionsController->retrieveFieldOptions();
            $App['categories'] = $category->forThisCompany($companyId)->toDigest();
            $App['designation'] = $designationModel->forThisCompany($companyId)->toDigest();
            $App['job_types'] = $jobTypeModel->forThisCompany($companyId)->toDigest();
            $App['work_schedules'] = $workScheduleModel->forThisCompany($companyId)->toDigest();
            $App['teams'] = $teamModel->forThisCompany($companyId)->toDigest();
            $App['employees'] = EmployeesDigestCollection::collection($companiesService->getCompanyEmployeesByUserId($this->companyId()));
            $App['expense_categories'] = $userService->getExpenseCategoriesByUserId($userId);
            $App['security_context'] = $userService->getUserSecrityContext($userId);
            $App['clients'] = $client->toDigest();
            $App['company_data'] = $companiesService->getCompany($companyId);
            $App['stats'] = $statisticsService->getAppData($companyId);
            $App['rbacl'] = $rbaclService->getRBACLData();
            $App['assigned_rbacl'] = $userService->getUserRole($userId);
            $App['brands'] = $brandModel->ForCompany($companyId)->toDigest();
            $App['test'] = $userService->isUserAllowedTo($userId, "ACL.View");
        }
        return $this->created(['app'=>$App]);
    }
}
