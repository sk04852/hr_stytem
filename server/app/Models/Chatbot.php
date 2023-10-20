<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class Chatbot
 * 
 * @property int $id
 * @property int $user_id
 * @property string $session_id
 * @property string|null $course_name
 * @property string|null $class_type
 * @property string|null $location
 * @property string|null $date_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class Chatbot extends BaseModel
{
	protected $table = 'chatbots';
	public static $snakeAttributes = false;

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'session_id',
		'course_name',
		'class_type',
		'location',
		'date_time'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
