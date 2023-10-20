<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MediaType
 * 
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string $description
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|MediaDrive[] $mediaDrives
 *
 * @package App\Models
 */
class MediaType extends BaseModel
{
	protected $table = 'media_types';
	public static $snakeAttributes = false;

	protected $casts = [
		'sort_order' => 'int'
	];

	protected $fillable = [
		'name',
		'short_name',
		'description',
		'sort_order'
	];

	public function mediaDrives()
	{
		return $this->hasMany(MediaDrive::class, 'media_type');
	}
}
