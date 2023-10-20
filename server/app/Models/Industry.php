<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Industry
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 *
 * @package App\Models
 */
class Industry extends BaseModel
{
	protected $table = 'industries';
	public static $snakeAttributes = false;

	protected $fillable = [
		'name'
	];
}
