<?php namespace App\Http\Controllers\Leads\Models;

use App\Http\Controllers\Comments\Models\Comment;
use Carbon\Carbon;
use App\Models\BaseModel;

/**
 * Class LeadComment
 *
 * @property int $id
 * @property int $lead_id
 * @property int $created_by
 * @property string $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $user
 * @property Lead $lead
 *
 * @package App\Models
 */
class LeadComment extends BaseModel
{
	protected $table = 'lead_comments';
	public static $snakeAttributes = false;

	protected $casts = [
		'lead_id' => 'int',
		'created_by' => 'int'
	];

	protected $fillable = [
		'lead_id',
		'created_by',
		'comment'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function lead()
	{
		return $this->belongsTo(Lead::class);
	}

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
