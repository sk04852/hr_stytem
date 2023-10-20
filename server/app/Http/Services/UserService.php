<?php
namespace App\Http\Services;

use Exception;
use DateTime;
use App\Models\GroupRole;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Acl\Models\Role;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Groups\Models\Group;
use App\Http\Controllers\Acl\Models\Permission;
use App\Http\Controllers\EmsEmployee\EmployeeController;
use App\Http\Controllers\UserGroup\Models\UserGroup;
use App\Http\Controllers\EmsEmployee\Models\Employee;
use App\Http\Controllers\People\Models\Person;
use App\Http\Controllers\People\PeopleController;
use App\Http\Resources\Users\UserAssignedRBACL;

class UserService extends PeopleController {
    private $model_;
    private $groupModel_;
    private $role_;
    private $permission_;
    private $employeeModel_;
    private $companyOwnerGroupId_ = 4;
    private $employeeGroupId_ = 3;
    private $guestGroupId_ = 5;

    private $rBACLService_;

    public function __construct(User $model,
                                Group $groupModel,
                                Role $role,
                                Permission $permission,
                                RBACLService $rBACLService,
                                Employee $employeeModel) {
        $this->model_ = $model;
        $this->groupModel_ = $groupModel;
        $this->employeeModel_ = $employeeModel;
        $this->role_ = $role;
        $this->permission_ = $permission;
        $this->rBACLService_ = $rBACLService;
    }

    public function getUserSecrityContext(int $userId) {
        return [
            'groups'=> $this->getUserGroups($userId)
        ];
    }

    public function getEmployeeEmailVerificationToken(Person $model)
    {
        return $this->setEmailVerificationToken(Str::random(12), $model);
    }

    public function setEmailVerificationToken($token , $user)
    {
        $user->email_verification_token_created_at = new DateTime();
        $user->email_verification_token = $token;
        $user->save();
    }

    public function addUserToGroup(int $userId, int $groupId) {
        $user = $this->getUser($userId);
        $group = $this->getGroup($groupId);

        if (!$user) {
            throw new Exception('User not found');
        }

        if (!$group) {
            throw new Exception('Group not found');
        }

        $userGroup = new UserGroup();
        $userGroup->user_id = $user->id;
        $userGroup->group_id = $group->id;
        return $userGroup->save();

    }

    public function removeUserFromGroup(int $userId, int $groupId) {
        $user = $this->getUser($userId);
        $group = $this->getGroup($groupId);

        if (!$user) {
            throw new Exception('User not found');
        }

        if (!$group) {
            throw new Exception('Group not found');
        }

        $relationship = UserGroup::where('group_id', $groupId)->where('user_id', $userId);
        if(!$relationship->exists()) {
            throw new Exception('User is not a part of this group');
        }

        return $relationship->delete();
    }

    public function getUserGroups(int $userId) {
        $user = $this->getUser($userId);
        if (!$user) {
            throw new Exception('User not found');
        }

        return UserGroup::where('user_id', $userId)->select('group_id')->get();
    }

    public function belongsToGroup(int $userId, int $groupId) {
        $groups = $this->getUserGroups($userId);
        $groupIds = $groups->map(function($group) { return $group->group_id; });
        return in_array($groupId, $groupIds->toArray());
    }

    public function getUser(int $userId) {
        return $this->model_->where('id', $userId)->first();
    }

    public function getGroup(int $groupId) {
        return $this->groupModel_->where('id', $groupId)->first();
    }

    public function getRole(int $roleId) {
        return $this->role_->where('id', $roleId)->first();
    }

    public function addRoleToGroup(int $roleId, int $groupId) {
        $role = $this->getRole($roleId);
        $group = $this->getGroup($groupId);

        if (!$role) {
            throw new Exception('Role not found');
        }

        if (!$group) {
            throw new Exception('Group not found');
        }

        $groupRole = new GroupRole();
        $groupRole->group_id = $groupId;
        $groupRole->role_id = $roleId;
        return $groupRole->save();
    }

    public function removeRoleFromGroup(int $roleId, int $groupId) {
        $role = $this->getRole($roleId);
        $group = $this->getGroup($groupId);

        if (!$role) {
            throw new Exception('Role not found');
        }

        if (!$group) {
            throw new Exception('Group not found');
        }

        $relationship = GroupRole::where('group_id', $groupId)->where('role_id', $roleId);
        if(!$relationship->exists()) {
            throw new Exception('Role is not a part of this group');
        }

        return $relationship->delete();
    }

    public function getAllPermissionsForRole(int $roleId) {
        $role = $this->getRole($roleId);
        if (!$role) {
            throw new Exception('Role not found');
        }

        return
        $this->permission_->select(['permissions.id', 'permissions.name as permission_name', 'roles.id as role_id', 'roles.name as role_name'])
        ->join('role_has_permissions', 'role_has_permissions.permission_id', 'permissions.id')
        ->join('roles', 'role_has_permissions.role_id', 'roles.id')
        ->where('role_has_permissions.role_id', $roleId)
        ->get();
    }


    // -Deprecated, Needs Improvements if reused
    public function getAllPermissionsForGroup(int $groupId) {
        $group = $this->getGroup($groupId);
        if (!$group) {
            throw new Exception('Group not found');
        }
        return
        DB::select("select
        permissions.id as id,
        permissions.name as permission_name,
        roles.id as role_id,
        roles.name as role_name from permissions
        LEFT JOIN role_has_permissions ON role_has_permissions.permission_id = permissions.id
        LEFT JOIN roles on role_has_permissions.role_id = roles.id
        WHERE roles.id IN (SELECT role_id FROM group_roles WHERE group_id = ?)", [$groupId]);
    }

    // -Deprecated, Needs Improvements if reused
    public function getAllPermissionsForGroups(array $groupIds) {
        return
        DB::select("select
        permissions.id as id,
        permissions.name as permission_name,
        roles.id as role_id,
        roles.name as role_name from permissions
        LEFT JOIN role_has_permissions ON role_has_permissions.permission_id = permissions.id
        LEFT JOIN roles on role_has_permissions.role_id = roles.id
        WHERE roles.id IN (SELECT role_id FROM group_roles WHERE group_id in( ? ))", [implode(",", $groupIds)]);
    }

    // Improvement: This can be cached.
    public function getUserRole(int $userId) {
        $user = null;
        if(auth() && auth()->user()->id == $userId) {
            $user = auth()->user();
        } else {
            $user = $this->getUser($userId);
        }
        $role = $this->rBACLService_->getRoleById($user->role_id);
        return new UserAssignedRBACL($role);
    }

    public function isUserAllowedTo(int $userId, string $permissionToCheck) {
        $role = $this->getUserRole($userId);
        return ($this->rBACLService_->canRoleBeByPassed($role->id) || $this->userHasSpecialConditions($userId) || $this->rBACLService_->roleHasPermission($role, $permissionToCheck));
    }

    public function userHasSpecialConditions(int $userId) {
        return false;
    }

    public function getEmployeeId(int $userId) {
        $employee = $this->employeeModel_->select(['id'])->where('user_id', $userId)->first();
        if(!$employee) {
            throw new Exception('Employee not found');
        }
        return $employee->id;
    }

    public function getEmployeeByUserId(int $userId) {
        $employee = $this->employeeModel_->where('user_id', $userId)->first();
        if(!$employee) {
            return null;
        }
        return $employee;
    }

    public function getCompanyId(int $userId) {
        $user = $this->getUser($userId);
        if(!$user) {
            throw new Exception('User not found');
        }
        return $user->company_id;
    }

    public function getUserIdsByCompanyId(int $companyId) {
        return $this->model_->select(['id'])->where('company_id', $companyId)->get();
    }

    public function registerCompany( $request ,$companiesService_ , $employeesService_)
    {
        try {
            $data = $request->all();
            $subDomain = ($request->has('sub_domain'))? $request->get('sub_domain'): $companiesService_->generateSubDomain($data['company_name']);

            if ($subDomain !== NULL) {
                $companyData = $request->only([
                                                'company_name',
                                                'company_website',
                                                'company_number',
                                                'vat_number',
                                                'founding_year',
                                                'mission_statement'
                ]);
                $companyData['sub_domain'] = $subDomain;
                $company = $companiesService_->createCompany($companyData);
                $user = registerUser($data['first_name'], $data['last_name'], $data['email'], $data['password'], EmployeeController::MODULE_NAME, true, $company->id);

                $this->employeeModel_ = $this->employeeModel_->newInstance();
                $this->employeeModel_->setRelation('user', $user);
                $this->employeeModel_->employee_number = $request->get('employee_number', $employeesService_->getEmployeeNumber());
                $this->employeeModel_->user_id = $user->id;
                $this->employeeModel_ = $this->ownerPreRegistration($this->employeeModel_);
                if ($this->employeeModel_->save()) {
                    $this->employeeModel_ = $this->ownerPostRegistration($this->employeeModel_);
                    return $this->created(['message' => PeopleController::SIGNUP_SUCCESS, 'profile' => $this->employeeModel_]);
                } else {
                    return $this->failed(['error' => PeopleController::SIGNUP_ERROR]);
                }
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    protected function ownerPreRegistration(Person $model)
    {
        return $model;
    }

    protected function ownerPostRegistration(Person $model)
    {
        // Owner by default gets two groups. 1: Company Owner, 2: Employee
        $groups = [
                    ['user_id'=> $model->user->id, 'group_id'=> $this->companyOwnerGroupId_],
                    ['user_id'=> $model->user->id, 'group_id'=> $this->employeeGroupId_]
        ];
        $EmployeeGroup = new UserGroup();
        $EmployeeGroup->insert($groups);
        return $model;
    }

    public function assignDefaultGroupToUser(int $userId) {
        $userGroup = new UserGroup();
        $userGroup->user_id = $userId;
        $userGroup->group_id = $this->getDefaultGroup()->id;
        $userGroup->save();
    }

    public function getDefaultGroup() {
        $defaultGroup = null;
        $companyId = getCompanyId();
        $defaultGroup = Group::where('company_id', $companyId)->where('is_default', 1)->first();
        if($defaultGroup == null) {
            $defaultGroup = Group::where('is_default', 1)->first();
        }
        return $defaultGroup;
    }

    public function getExpenseCategoriesByUserId(int $userId) {
        $user = $this->getUser($userId);
        if(is_null($user))
            throw new Exception("User not found, unable to fetch expense categories");
        return $user->expenseCategories()->get();
    }

    public function isModerator(int $userId) {
        $groupId = 2;
        return $this->belongsToGroup($userId, $groupId);
    }

    public function isCompanyOwner(int $userId) {
        return $this->belongsToGroup($userId, $this->companyOwnerGroupId_);
    }

    public function isEmployee(int $userId) {
        return $this->belongsToGroup($userId, $this->employeeGroupId_);
    }

    public function isGuest(int $userId) {
        return $this->belongsToGroup($userId, $this->guestGroupId_);
    }

}

?>
