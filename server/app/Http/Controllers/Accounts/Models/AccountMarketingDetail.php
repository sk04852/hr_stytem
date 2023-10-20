<?php

namespace App\Http\Controllers\Accounts\Models;

use App\Models\BaseModel;
use App\Models\Account;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountMarketingDetail extends BaseModel
{
    use SoftDeletes;

    protected $table = 'account_marketing_details';
    public static $snakeAttributes = false;

    protected $casts = [
        'account_id' => 'int',
    ];

    protected $fillable = [
        'account_id',
        'referrer_id',
        'affiliate_id',
        'banner_id',
        'mtg_1',
        'mtg_2',
        'network',
        'media',
        'zone',
        'priority',
        'ad_size',
        'ad_id',
        'ad_group',
        'utm_campaign',
        'utm_medium',
        'utm_source',
        'utm_term',
        'utm_content',
        'utm_category',
        'campaign_id',
        'profile',
        'sem_mt',
        'gcl_id',
        'sem_sq',
        'sem_position',
        'lead_notification_url',
        'lead_ext_notify',
        'demo_registration_ext_notify',
        'live_registration_ext_notify',
        'ftd_ext_notify',
        'agent_account',
        'referral_channel',
    ];
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
