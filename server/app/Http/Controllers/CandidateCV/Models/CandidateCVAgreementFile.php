<?php

namespace App\Http\Controllers\CandidateCV\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateCVAgreementFile extends Model
{
    use SoftDeletes;

    protected $table = 'candidatecv_agreement_files';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'candidatecv_agreement_id',
        'file_name',
        'path'
    ];

    /**
     * Get the CandidateCV record associated with the Job History.
     */
    public function candidatecvAgreement()
    {
        return $this->belongsTo('App\Http\Controllers\CandidateCV\Models\CandidateCVAgreement', 'candidatecv_agreement_id', 'id');
    }

    public function getPathAttribute($value)
    {
        return asset('storage/'.$value);
    }


}
