<?php

namespace App\Http\Controllers\TradingPlatforms\Models;

use App\Http\Controllers\Companies\Models\Company;
use App\Http\Controllers\Options\Models\FieldOption;
use App\Http\Controllers\Users\Models\User;
use App\Models\BaseModel;

class TradingPlatform extends BaseModel
{
	protected $table = 'trading_platforms';
	public static $snakeAttributes = false;
	protected $guard_name = 'api';

	protected $fillable = [
		'company_id',
		'login',
		'password',
		'port',
		'environment',
		'platform',
		'crm',
        'host',
		'param_1',
		'created_by',
		'last_updated_by',
	];

	public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

	public function lastUpdatedBy() {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

	public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function environment() {
        return $this->belongsTo(FieldOption::class, 'environment');
    }

    public function platform() {
        return $this->belongsTo(FieldOption::class, 'platform');
    }

    public function crm() {
        return $this->belongsTo(FieldOption::class, 'crm');
    }


	public function scopeWithCompany($query)
	{
		return $query->with(['company' => function ($query) {
			$query->select(['id', 'name', 'primary_email']);
		}]);
	}

	public function scopeWithCreatedBy($query)
	{
		return $query->with(['createdBy' => function ($query) {
			$query->select(['id', 'first_name','last_name','email']);
		}]);
	}
	public function scopeWithLastUpdatedBy($query)
	{
		return $query->with(['lastUpdatedBy' => function ($query) {
			$query->select(['id', 'first_name','last_name','email']);
		}]);
	}

    public function scopeWithEnvironment($query)
	{
		return $query->with(['environment' => function ($query) {
			$query->select(['id', 'name','short_name','description']);
		}]);
	}

    public function scopeWithPlatform($query)
	{
		return $query->with(['platform' => function ($query) {
			$query->select(['id', 'name','short_name','description']);
		}]);
	}

    public function scopeWithCRM($query)
	{
		return $query->with(['crm' => function ($query) {
			$query->select(['id', 'name','short_name','description']);
		}]);
	}
}
