<?php namespace App\Http\Controllers\Leads\Models;

use Carbon\Carbon;
use App\Models\BaseModel;

/**
 * Class LeadMarketingDetail
 *
 * @property int $id
 * @property int $lead_id
 * @property int|null $referrer_id
 * @property int|null $affiliate_id
 * @property string|null $mtg_1
 * @property string|null $mtg_2
 * @property string|null $network
 * @property string|null $media
 * @property string|null $zone
 * @property string|null $priority
 * @property string|null $ad_size
 * @property string|null $ad_id
 * @property string|null $ad_group
 * @property string|null $utm_campaign
 * @property string|null $utm_medium
 * @property string|null $utm_source
 * @property string|null $utm_term
 * @property string|null $utm_content
 * @property string|null $utm_category
 * @property string|null $campaign_id
 * @property string|null $profile
 * @property string|null $sem_mt
 * @property string|null $gcl_id
 * @property string|null $sem_sq
 * @property string|null $sem_position
 * @property string|null $lead_notification_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Lead $lead
 *
 * @package App\Models
 */
class LeadMarketingDetail extends BaseModel
{
	protected $table = 'lead_marketing_details';
	public static $snakeAttributes = false;

	protected $casts = [
		'lead_id' => 'int',
		'referrer_id' => 'int',
		'affiliate_id' => 'int'
	];

	protected $fillable = [
		'lead_id',
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
		'lead_notification_url'
	];

	public function lead()
	{
		return $this->belongsTo(Lead::class);
	}
}
