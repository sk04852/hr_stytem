<?php

namespace App\Http\Controllers\Acl\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $profile_id
 * @property integer $permission_id
 * @property AclPermission $aclPermission
 * @property AclProfile $aclProfile
 */
class ProfilePermission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acl_profile_permissions';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['profile_id', 'permission_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}
