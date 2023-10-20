<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FieldOption
 * 
 * @property int $id
 * @property int $type_id
 * @property string $name
 * @property string $short_name
 * @property string $description
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property FieldOptionType $fieldOptionType
 *
 * @package App\Models
 */
class FieldOption extends BaseModel
{
	use SoftDeletes;
	protected $table = 'field_options';
	public static $snakeAttributes = false;

	protected $casts = [
		'type_id' => 'int',
		'sort_order' => 'int'
	];

	protected $fillable = [
		'type_id',
		'name',
		'short_name',
		'description',
		'sort_order'
	];

	public function fieldOptionType()
	{
		return $this->belongsTo(FieldOptionType::class, 'type_id');
	}
}
