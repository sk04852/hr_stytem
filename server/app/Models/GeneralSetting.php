<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class GeneralSetting
 * 
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string|null $value
 * @property string|null $description
 * @property bool $autoload
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 *
 * @package App\Models
 */
class GeneralSetting extends BaseModel
{
	protected $table = 'general_settings';
	public static $snakeAttributes = false;

	protected $casts = [
		'autoload' => 'bool'
	];

	protected $fillable = [
		'type',
		'name',
		'value',
		'description',
		'autoload'
	];
}
