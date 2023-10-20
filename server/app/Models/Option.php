<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class Option
 * 
 * @property int $id
 * @property string $name
 * @property string $value
 * @property bool $autoload
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Option extends BaseModel
{
	protected $table = 'options';
	public static $snakeAttributes = false;

	protected $casts = [
		'autoload' => 'bool'
	];

	protected $fillable = [
		'name',
		'value',
		'autoload'
	];
}
