<?php
namespace App\Http\Services;
use App\Http\Controllers\Acl\Models\Permission;
use App\Http\Controllers\Acl\Models\Profile;
use App\Http\Controllers\Acl\Models\Role;
use App\Http\Controllers\Acl\Models\RoleProfile;
use App\Http\Resources\RBACL\PermissionsCollection;
use App\Http\Resources\Users\UserAssignedRBACL;
use App\Models\Module;
use Exception;

class RBACLService {

    private $roleModel_;
    private $profileModel_;
    private $moduleModel_;
    private $byPassableRoles_ = [1, 2];

    public function __construct(Role $role,
                                RoleProfile $roleProfile,
                                Module $moduleModel,
                                Profile $profile) {
        $this->roleModel_ = $role;
        $this->profileModel_ = $profile;
        $this->roleProfileModel_ = $roleProfile;
        $this->moduleModel_ = $moduleModel;
    }

    public function addProfileToRole(int $profileId, int $roleId) {
        $profile = $this->profileModel_->where('id', $profileId)->first();
        $role = $this->roleModel_->where('id', $roleId)->first();
        if(!$profile)
            throw new Exception("Invalid profile");
        if(!$role)
            throw new Exception("Invalid role");
        $roleProfile = $this->roleProfileModel_->fill(['role_id'=> $role->id, 'profile_id'=> $profile->id]);
        return $role->profiles()->save($roleProfile);
    }

    public function getProfilesByRole(int $roleId) {
        $role = $this->roleModel_->where('id', $roleId)->first();
        if(!$role)
            throw new Exception("Invalid profile");
        return $role->profiles;
    }

    public function removeProfileFromRole(int $profileId, int $roleId) {
        $profile = $this->profileModel_->where('id', $profileId)->first();
        $role = $this->roleModel_->where('id', $roleId)->first();
        if(!$profile)
            throw new Exception("Invalid profile");
        if(!$role)
            throw new Exception("Invalid role");
        return $this->roleProfileModel_->where('profile_id', $profileId)->where('role_id', $roleId)->delete();
    }

    public function getProfile(int $profileId) {
        $this->profileModel_->where('id', $profileId)->first();
    }

    public function assignRoleToUser() {

    }

    public function syncProfilePermissions($profile, $permissions) {
        $permissionsCollection = collect($permissions);
        $permissions = $permissionsCollection->flatten(1)->values();
        $profile->permissions()->sync($permissions);
    }

    public function getSystemPermissions() {
        $modules = $this->moduleModel_->where('status', 'Active')->get();
        return PermissionsCollection::collection($modules);
    }

    public function attachProfilesToRole(int $roleId, array $profiles) {
        $role = $this->roleModel_->where('id', $roleId)->first();
        if(!$role)
            throw new Exception("Role not found, unable to assign profiles.");
        $role->profiles()->sync($profiles);
    }

    public function getRBACLData() {
        $data = [];
        $data['roles'] = $this->roleModel_->get();
        $data['profiles'] = $this->profileModel_->get();
        return $data;
    }

    public function getRoleById(int $id) {
        return $this->roleModel_->where('id', $id)->with('profiles')->first();
    }

    public function roleHasPermission(UserAssignedRBACL $role, string $permissionToCheck) {
        $role = $role->toArray(null);
        $permissionToCheck = strtolower($permissionToCheck);
        foreach($role["permissions"] as $permissions) {
            foreach($permissions as $permission) {
                if(strtolower($permission->name) === $permissionToCheck) {
                    return true;
                }
            }
        }
        return false;
    }

    public function canRoleBeByPassed(int $roleId) {
        return in_array($roleId, $this->byPassableRoles_);
    }
}

?>
