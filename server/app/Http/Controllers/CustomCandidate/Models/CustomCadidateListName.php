<?php

namespace App\Http\Controllers\CustomCandidate\Models;

use App\Models\UserPr;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomCadidateListName extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'custom_candidate_list_names';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'list_name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function userPr(){
        return $this->belongsTo(UserPr::class, 'user_id', 'id');
    }

    public function candidates()
    {
        return $this->belongsToMany('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'custom_candidate_lists', 'list_name_id', 'candidatecv_id');
    }

}
