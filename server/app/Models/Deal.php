<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Deal
 * 
 * @property int $id
 * @property string $name
 * @property float|null $amount
 * @property string|null $currency
 * @property int $contact_id
 * @property Carbon|null $expected_close_date
 * @property string|null $pipeline
 * @property string|null $sale_stage
 * @property int|null $assigned_to
 * @property string|null $lead_source
 * @property string|null $deal_type
 * @property int|null $probability_percentage
 * @property string|null $lost_reason
 * @property int|null $weighted_revenue
 * @property string|null $description
 * @property int $created_by
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Deal extends BaseModel
{
	use SoftDeletes;
	protected $table = 'deals';
	public static $snakeAttributes = false;

	protected $casts = [
		'amount' => 'float',
		'contact_id' => 'int',
		'assigned_to' => 'int',
		'probability_percentage' => 'int',
		'weighted_revenue' => 'int',
		'created_by' => 'int'
	];

	protected $dates = [
		'expected_close_date'
	];

	protected $fillable = [
		'name',
		'amount',
		'currency',
		'contact_id',
		'expected_close_date',
		'pipeline',
		'sale_stage',
		'assigned_to',
		'lead_source',
		'deal_type',
		'probability_percentage',
		'lost_reason',
		'weighted_revenue',
		'description',
		'created_by'
	];
}
