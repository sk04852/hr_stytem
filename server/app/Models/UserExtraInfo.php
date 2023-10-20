<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserExtraInfo
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $default_record_view
 * @property string|null $department
 * @property string|null $fax
 * @property string|null $language
 * @property string|null $home_phone
 * @property string|null $mobile_phone
 * @property string|null $office_phone
 * @property int|null $report_to
 * @property string|null $internal_mail_composer
 * @property string|null $secondary_email
 * @property string|null $secondary_phone
 * @property string|null $signature
 * @property string|null $title
 * @property string|null $theme
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class UserExtraInfo extends BaseModel
{
	use SoftDeletes;
	protected $table = 'user_extra_info';
	public static $snakeAttributes = false;

	protected $casts = [
		'user_id' => 'int',
		'report_to' => 'int'
	];

	protected $fillable = [
		'user_id',
		'default_record_view',
		'department',
		'fax',
		'language',
		'home_phone',
		'mobile_phone',
		'office_phone',
		'report_to',
		'internal_mail_composer',
		'secondary_email',
		'secondary_phone',
		'signature',
		'title',
		'theme'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
