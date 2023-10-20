<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplicants extends Model
{
    use HasFactory;
    //update 
    protected $table = 'timeline';

    protected $fillable = ['action_ID', 'candidate_ID', 'userpr_ID', 'action_name', 'date_added', 'comments', 'job_ID'];
    
}
