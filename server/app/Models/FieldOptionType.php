<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FieldOptionType
 * 
 * @property int $id
 * @property string $type_description
 * @property string $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|FieldOption[] $fieldOptions
 *
 * @package App\Models
 */
class FieldOptionType extends BaseModel
{
	use SoftDeletes;
	protected $table = 'field_option_types';
	public static $snakeAttributes = false;

	protected $fillable = [
		'type_description',
		'comment'
	];

	public function fieldOptions()
	{
		return $this->hasMany(FieldOption::class, 'type_id');
	}
}
