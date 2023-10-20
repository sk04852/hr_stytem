<?php

namespace App\Http\Controllers\Accounts\Models;

use App\Models\BaseModel;
use App\Models\Account;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountLeadConversion extends BaseModel
{
    use SoftDeletes;

    protected $table = 'account_lead_converstion_details';
    public static $snakeAttributes = false;

    protected $casts = [
        'account_id' => 'int',
        'lead_id' => 'int',
    ];

    protected $fillable = [
        'account_id',
        'is_converted_from_lead',
        'lead_creation_time',
        'lead_id',
        'external_lead_id',
        'lead_supplier',
        'lead_source',
        'page_url',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
