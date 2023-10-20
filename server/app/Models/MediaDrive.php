<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MediaDrive
 * 
 * @property int $id
 * @property string $file_name
 * @property int|null $media_directory_id
 * @property int $media_type
 * @property int|null $relation_id
 * @property bool $is_default
 * @property int $media_size
 * @property int $media_height
 * @property int $media_width
 * @property string|null $media_title
 * @property string|null $media_alt_text
 * @property string|null $media_description
 * @property string|null $media_mime_type
 * @property string|null $media_extension
 * @property string|null $media_url
 * @property bool $media_is_image
 * @property string|null $thumbnails
 * @property string|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property MediaDirectory $mediaDirectory
 * @property MediaType $mediaType
 * @property Collection|Document[] $documents
 * @property Collection|MediaDriveRelation[] $mediaDriveRelations
 *
 * @package App\Models
 */
class MediaDrive extends BaseModel
{
	use SoftDeletes;
	protected $table = 'media_drive';
	public static $snakeAttributes = false;

	protected $casts = [
		'media_directory_id' => 'int',
		'media_type' => 'int',
		'relation_id' => 'int',
		'is_default' => 'bool',
		'media_size' => 'int',
		'media_height' => 'int',
		'media_width' => 'int',
		'media_is_image' => 'bool'
	];

	protected $fillable = [
		'file_name',
		'media_directory_id',
		'media_type',
		'relation_id',
		'is_default',
		'media_size',
		'media_height',
		'media_width',
		'media_title',
		'media_alt_text',
		'media_description',
		'media_mime_type',
		'media_extension',
		'media_url',
		'media_is_image',
		'thumbnails',
		'data'
	];

	public function mediaDirectory()
	{
		return $this->belongsTo(MediaDirectory::class);
	}

	public function mediaType()
	{
		return $this->belongsTo(MediaType::class, 'media_type');
	}

	public function documents()
	{
		return $this->hasMany(Document::class, 'media_id');
	}

	public function mediaDriveRelations()
	{
		return $this->hasMany(MediaDriveRelation::class, 'media_id');
	}
}
