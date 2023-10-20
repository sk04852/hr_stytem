<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Controllers\Options\Models;

use Carbon\Carbon;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Collection;

class FieldOptionType extends BaseModel
{
	protected $table = 'field_option_types';
	public static $snakeAttributes = false;

	protected $fillable = [
		'type_description',
		'comment'
	];

    public static function boot() {
        parent::boot();
        static::deleting(function($fieldOptionType) {
             $fieldOptionType->fieldOptions()->delete();
             $fieldOptionType->companyMap()->delete();
        });
    }

	public function fieldOptions()
	{
		return $this->hasMany(FieldOption::class, 'type_id')->select(['id', 'name', 'description', 'type_id', 'sort_order']);
	}

    public function companyMap() {
        return $this->hasMany(FieldOptionTypeCompanyMap::class);
    }


}
