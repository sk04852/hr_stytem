<?php
namespace App\Http\Controllers\Acl\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $role_id
 * @property integer $profile_id
 * @property string $created_at
 * @property string $updated_at
 * @property AclProfile $aclProfile
 * @property AclRole $aclRole
 */
class RoleProfile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acl_role_profiles';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['role_id', 'profile_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo('App\Http\Controllers\Acl\Models\AclProfile', 'profile_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo('App\Http\Controllers\Acl\Models\AclRole', 'role_id');
    }
}
