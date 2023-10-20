<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class History extends Model
{

    protected $table = 'candidate_history';
    public $timestamps = true;

    protected $fillable = [
        'reference_table',
        'reference_id',
        'actor_id',
        'body',
        'comments'
    ];

}
