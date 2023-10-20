<?php

namespace App\Http\Controllers\Options\Models;
use App\Models\BaseModel;

class FieldOption extends BaseModel
{
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

    public function toDigest() {
        return $this->select(['id', 'type_id','name', 'description'])->get();
	}
}
