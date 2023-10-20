<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MediaDirectory
 * 
 * @property int $id
 * @property string $name
 * @property string $location
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 * @property Collection|MediaDrive[] $mediaDrives
 *
 * @package App\Models
 */
class MediaDirectory extends BaseModel
{
	use SoftDeletes;
	protected $table = 'media_directories';
	public static $snakeAttributes = false;

	protected $casts = [
		'created_by' => 'int'
	];

	protected $fillable = [
		'name',
		'location',
		'created_by'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function mediaDrives()
	{
		return $this->hasMany(MediaDrive::class);
	}
}
