<?php

namespace App\Http\Controllers\Acl\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $created_by
 * @property boolean $is_assigned_to_all
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property AclRolePermission[] $aclRolePermissions
 * @property AclRoleProfile[] $aclRoleProfiles
 */
class Role extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acl_roles';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['created_by', 'is_assigned_to_all', 'name', 'slug', 'description', 'status', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Http\Controllers\Acl\Models\User', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aclRolePermissions()
    {
        return $this->hasMany('App\Http\Controllers\Acl\Models\RolePermission', 'role_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'acl_role_profiles')->select(['acl_profiles.id as id', 'name', 'description']);
    }
}
