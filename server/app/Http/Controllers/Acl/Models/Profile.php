<?php

namespace App\Http\Controllers\Acl\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $created_by
 * @property string $name
 * @property string $description
 * @property string $attributes
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property AclRoleProfile[] $aclRoleProfiles
 */
class Profile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acl_profiles';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['created_by', 'name', 'description', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roleProfiles()
    {
        return $this->hasMany('App\AclRoleProfile', 'profile_id');
    }

    public function roles() {
        return $this->belongsToMany('App\Http\Controllers\Acl\Models\Role');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'acl_profile_permissions')->select('acl_permissions.id as permission_id', 'acl_permissions.module_id', 'acl_permissions.name', 'acl_permissions.type');
    }
}
