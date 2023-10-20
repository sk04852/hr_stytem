<?php
namespace App\Http\Controllers\Timelines\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimelineComment extends Model
{
    use SoftDeletes; 
    
	protected $table = 'timelines_comments';

	public $timestamps = true;

	public static $snakeAttributes = false;

    protected $fillable = [
        'parent_id',
        'timeline_id',
        'user_pr_id',
        'body',
    ];

    protected $hidden = [
//        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the timeline record associated with the comment.
     */
    public function timeline()
    {
        return $this->belongsTo('App\Http\Controllers\Timelines\Models\Timeline', 'timeline_id', 'id');
    }
}
