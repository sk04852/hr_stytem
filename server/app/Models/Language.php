<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class Language
 * 
 * @property int $id
 * @property string $name
 * @property string $prefix
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Language extends BaseModel
{
	protected $table = 'languages';
	public static $snakeAttributes = false;

	protected $fillable = [
		'name',
		'prefix'
	];
}
