<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Controllers\Medias\Models;

use App\Models\BaseModel;

class MediaType extends BaseModel
{
	protected $table = 'media_types';
	public static $snakeAttributes = false;

	protected $casts = [
		'sort_order' => 'int'
	];

	protected $fillable = [
		'name',
		'short_name',
		'description',
		'sort_order'
    ];

    protected static function booted()
    {
        //static::invalidatesCache();
    }

	public function mediaDrives()
	{
		return $this->hasMany(MediaDrive::class, 'media_type');
	}

	public function mediaDriveRelations()
	{
		return $this->hasMany(MediaDriveRelation::class);
    }

    public function toDigest() {
        return $this->select(['id', 'name', 'short_name'])->get();
    }
}
