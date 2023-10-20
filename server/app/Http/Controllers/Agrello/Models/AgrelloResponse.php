<?php
/**
 * AgrelloResponse
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\Agrello\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgrelloResponse extends Model
{
    use SoftDeletes;

    public const CREATE_TEMPLATE_MULTIPART_CONTENT = 'CreateTemplateMultipartContent';
    public const DELETE_TEMPLATE = 'DeleteTemplate';
    public const CREATE_CONTAINER_BY_TEMPLATE_REQUEST = 'CreateContainerByTemplateRequest';

    public const FAILED = 0;
    public const CREATED = 1;
    public const WAITING = 2;
    public const DOWNLOADED = 3;
    public const COMPLETED = 4;

    public const ZIP = 'ZIP';
    public const ASICE = 'ASICE';
    public const PDF = 'PDF';

    protected $table = 'agrello_responses';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'candidatecv_id',
        'user_pr_id',
        'event',
        'event_id',
        'response',
        'additional_data',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function userPr (){
        return $this->belongsTo('App\Models\UserPr', 'user_pr_id', 'id');
    }

    public function candidatecv (){
        return $this->belongsTo('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_id', 'id');
    }



}
