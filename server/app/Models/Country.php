<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;



/**
 * Class Country
 * 
 * @property int $id
 * @property string $sort_name
 * @property string $name
 * @property int $phone_code
 *
 * @package App\Models
 */
class Country extends BaseModel
{
	protected $table = 'countries';
	public $timestamps = false;
	public static $snakeAttributes = false;

	protected $casts = [
		'phone_code' => 'int'
	];

	protected $fillable = [
		'sort_name',
		'name',
		'phone_code'
    ];
    
    public function toDigest() {
        return $this->select(['id', 'name'])->OrderBy('name')->get();
    }
}
