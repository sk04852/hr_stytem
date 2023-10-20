<?php

namespace App\Http\Controllers\CandidateCVProfiles\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateCVProfile extends Model
{
    protected $table = 'candidatecv-pr';
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
        'candiadte-ID',
        'Int-code',
        'Email',
        'Phone-number',
        'photo',
        'Date-added',
        'Userpr-ID',
        'Nr-of-jobs',
        'Time-from-last act',
        'Date-of-birth',
        'Lang-lvl',
        'file-ID',
        'Tag-ID',
        'skill-iD',
        'Action-iD',
        'consent-iD',
    ];

    public function candidateCVProfileFilter($request){

        return CandidateCVProfile::when(!empty($request->cv_id), function ($query) use ($request) {
            return $query->where('candiadte-ID', '=', $request->cv_id);
        })
        ->when(!empty($request->IntCode), function ($query) use ($request) {
            $query->where('Int-code', $request->IntCode);
        });
    }

}
