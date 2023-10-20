<?php

namespace App\Http\Controllers\CandidateCV\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgreementStatus extends Model
{
    use SoftDeletes;

    protected $table = 'agreement_statuses';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the Agreements record associated with the Agreement Status.
     */
    public function candidatecvAgreements()
    {
        return $this->hasMany('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_id', 'id');
    }

}
