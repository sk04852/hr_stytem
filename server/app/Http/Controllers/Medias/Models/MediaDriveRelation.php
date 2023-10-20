<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Controllers\Medias\Models;

use App\Models\BaseModel;

class MediaDriveRelation extends BaseModel
{
	protected $table = 'media_drive_relations';
    public static $tableName = 'media_drive_relations';

	public static $snakeAttributes = false;


	protected $casts = [
		'media_id' => 'int',
		'module_id' => 'int',
		'relation_id' => 'int'
	];

	protected $fillable = [
		'media_id',
		'module_id',
		'relation_id'
    ];

	public function media()
	{
		return $this->belongsTo(Media::class, 'media_id');
	}
}
