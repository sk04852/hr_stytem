<?php

namespace App\Http\Controllers\Acl\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $module_id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 * @property Module $module
 */
class Permission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'acl_permissions';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['module_id', 'name', 'description', 'type', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module()
    {
        return $this->belongsTo('App\Module');
    }
}
