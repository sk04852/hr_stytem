<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MediaDriveRelation
 * 
 * @property int $id
 * @property int $media_id
 * @property int $module_id
 * @property int $relation_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property MediaDrive $mediaDrive
 * @property Module $module
 *
 * @package App\Models
 */
class MediaDriveRelation extends BaseModel
{
	use SoftDeletes;
	protected $table = 'media_drive_relations';
	public static $snakeAttributes = false;

	protected $casts = [
		'media_id' => 'int',
		'module_id' => 'int',
		'relation_id' => 'int'
	];

	protected $fillable = [
		'media_id',
		'module_id',
		'relation_id'
	];

	public function mediaDrive()
	{
		return $this->belongsTo(MediaDrive::class, 'media_id');
	}

	public function module()
	{
		return $this->belongsTo(Module::class);
	}
}
