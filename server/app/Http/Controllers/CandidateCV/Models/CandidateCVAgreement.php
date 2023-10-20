<?php

namespace App\Http\Controllers\CandidateCV\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateCVAgreement extends Model
{
    use SoftDeletes;

    protected $table = 'candidatecv_agreements';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'candidatecv_id',
        'user_pr_id',
        'agreement_status_id'
    ];

    /**
     * Get the Status record associated with the Agreement.
     */
    public function agreementStatus()
    {
        return $this->belongsTo('App\Http\Controllers\CandidateCV\Models\AgreementStatus', 'agreement_status_id', 'id');
    }

    /**
     * Get the CandidateCV record associated with the Job History.
     */
    public function candidatecv()
    {
        return $this->belongsTo('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_id', 'id');
    }

    /**
     * Get the userPr record associated with the Agreement. Signer of the agreement
     */
    public function userPr()
    {
        return $this->belongsTo('App\Models\UserPr', 'user_pr_id', 'user_pr');
    }

    /**
     * Get the Files record associated with the agreement.
     */
    public function files()
    {
        return $this->hasMany('App\Http\Controllers\CandidateCV\Models\CandidateCVAgreementFile', 'candidatecv_agreement_id', 'id');
    }


}
