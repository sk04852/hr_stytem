<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Controllers\Accounts\Models;

use App\Models\BaseModel;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AccountTradingExperience
 *
 * @property int $id
 * @property int $account_id
 * @property bool $working_in_financial_industry
 * @property string|null $years_in_financial_industry
 * @property bool $knowledge_of_cfds
 * @property bool $have_previously_traded_cfds
 * @property string|null $cfds_trainind_frequency
 * @property string|null $forex_trading_experience
 * @property string|null $spread_betting_trading_frequence
 * @property string $options_trading_frequency
 * @property bool $have_expereice_trading_pos_and_ror
 * @property bool $have_expereice_trading_at_market_prices
 * @property bool $have_expereice_trading_at_limited_cost
 * @property bool $have_expereice_trading_using_trailing_stop
 * @property bool $have_expereice_trading_using_a_stop_price
 * @property bool $have_expereice_trading_using_take_profit
 * @property bool $experience_or_qualification_in_trading
 * @property string|null $how_would_the_client_feel_after_lost_of_deposit
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Account $account
 *
 * @package App\Models
 */
class AccountTradingExperience extends BaseModel
{
	use SoftDeletes;
	protected $table = 'account_trading_experience';
	public static $snakeAttributes = false;

	protected $casts = [
		'account_id' => 'int',
		'working_in_financial_industry' => 'bool',
		'knowledge_of_cfds' => 'bool',
		'have_previously_traded_cfds' => 'bool',
		'have_expereice_trading_pos_and_ror' => 'bool',
		'have_expereice_trading_at_market_prices' => 'bool',
		'have_expereice_trading_at_limited_cost' => 'bool',
		'have_expereice_trading_using_trailing_stop' => 'bool',
		'have_expereice_trading_using_a_stop_price' => 'bool',
		'have_expereice_trading_using_take_profit' => 'bool',
		'experience_or_qualification_in_trading' => 'bool'
	];

	protected $fillable = [
		'account_id',
		'working_in_financial_industry',
		'years_in_financial_industry',
		'knowledge_of_cfds',
		'have_previously_traded_cfds',
		'cfds_trading_frequency',
		'forex_trading_frequency',
		'spread_betting_trading_frequency',
		'options_trading_frequency',
		'have_experienced_trading_pos_options_and_ror',
		'have_experienced_with_trading_at_market_prices',
		'have_experienced_with_trading_at_limited_cost',
		'have_experienced_with_trading_using_trailing_stop',
		'Have_Experienced_With_Trading_Using_A_Stop_Price',
		'have_Experienced_With_Trading_Using_Take_Profit',
		'experience_or_qualification_in_trading',
        'experience_in_cfds_frequency',
        'experience_in_cfds_average',
        'experience_in_other_derivatives_average',
        'contract_$10000_leverage_1_50_margin_4',
        'experience_in_other_derivatives_frequency',
        'position_close_equity_reach_maintenance_margin',
		'how_would_the_client_feel_after_lost_of_deposit',

        'higher_leverage_equals_higher_volume',
        'experience_in_shares_bonds_average',
        'experience_in_shares_bonds_frequency',
        'related_education_level',
        'average_leverage_level_of_past_investments',
        'yearly_volume',
        'lbl_past_year_transactions_value',
        'lbl_past_year_transactions_number',
        'level_of_experience',
        'in_the_past_2_years',

        'the_size_of_financial_instrument_portfolio',
        'what_percentage_money_accepted_to_risk_losing',
        'experience_question_4',
        'experience_question_5',
        'experience_question_6',
        'experience_question_7',
        'experience_question_8',
	];

	public function account()
	{
		return $this->belongsTo(Account::class);
	}
}
