<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class GenericCity
 * 
 * @property int $id
 * @property string $name
 * @property string|null $param_1
 * @property string|null $param_2
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class GenericCity extends BaseModel
{
	use SoftDeletes;
	protected $table = 'generic_cities';
	public static $snakeAttributes = false;

	protected $fillable = [
		'name',
		'param_1',
		'param_2'
	];
}
