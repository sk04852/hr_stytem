<?php
namespace App\Http\Controllers\TradingAccounts\Models;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\User;
use App\Models\BaseModel;
use App\Events\WorkflowEvent;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\TradingAccounts\PlatformDetails\Models\TradingAccountPlatformDetail; 



class TradingAccount extends BaseModel
{
    use SoftDeletes;
	protected $table = 'trading_accounts';
	public static $snakeAttributes = false;

	protected $casts = [
		'account_id' => 'int',
		'assigned_to' => 'int',
		'tp_account_id' => 'int'
	];

	protected $fillable = [
        'account_id',
        'currency',
		'assigned_to',
		'tp_account_id',
		'account_type',
		'platform',
		'is_additional_account',
		'trading_platform_group',
		'ib',
		'account_stage'
	];

	//workflow
    protected static function booted()
    {
        static::saved(function($model){
            dd($model);
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'saved'));
        });

        static::created(function($model){
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'create'));
        });

        static::updated(function($model){
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'update'));
        });
        
        static::deleted(function($model){
            dd($model);
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'delete'));
        });
    }

	public function account()
	{
		return $this->belongsTo(Account::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'assigned_to');
	}

	public function tradingAccountPlatformDetails()
	{
		return $this->hasMany(TradingAccountPlatformDetail::class, 'trading_platform_account_id');
    }

    public function setPlatform(string $platform) {
        return $this->platform = $platform;
    }

    public function setTradingPlatformDetails(TradingAccountPlatformDetail $model) {
        $this->trading_platform_details = $model;
    }
}
