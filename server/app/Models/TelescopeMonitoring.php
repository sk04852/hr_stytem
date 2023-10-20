<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;



/**
 * Class TelescopeMonitoring
 * 
 * @property string $tag
 *
 * @package App\Models
 */
class TelescopeMonitoring extends BaseModel
{
	protected $table = 'telescope_monitoring';
	public $incrementing = false;
	public $timestamps = false;
	public static $snakeAttributes = false;

	protected $fillable = [
		'tag'
	];
}
