<?php
namespace App\Http\Controllers\Timelines\Models;

use Illuminate\Database\Eloquent\Model;

class TimelineActionType extends Model
{
	protected $table = 'timeline_action_types';

	public $timestamps = true;

	public static $snakeAttributes = false;

    protected $fillable = [
        'id',
        'name',
    ];


    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the Comments record associated with the timeline.
     */
    public function candidatecv()
    {
        return $this->hasOne('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'timeline_action_type_id', 'id');
    }
}
