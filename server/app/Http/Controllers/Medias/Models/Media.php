<?php

namespace App\Http\Controllers\Medias\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends BaseModel
{
    use SoftDeletes;
	protected $table = 'media_drive';
	public static $tableName = 'media_drive';
	public static $snakeAttributes = false;

	protected $fillable = [
        'file_name',
        'media_type',
        'media_directory_id',
        'is_default',
        'media_size',
        'media_height',
        'media_weight',
        'media_title',
        'media_alt_text',
        'media_description',
        'media_mime_type',
        'media_extension',
        'media_url',
        'media_is_image',
        'thumbnails',
        'data',
    ];

    public function relations() {
        return $this->hasMany(Media::class);
    }

    public function type()
	{
		return $this->belongsTo(MediaType::class, 'media_type', 'id');
	}
}
