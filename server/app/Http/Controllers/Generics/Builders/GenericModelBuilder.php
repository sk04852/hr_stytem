<?php

namespace App\Http\Controllers\Generics\Builders;

use App;

class GenericModelBuilder
{
    private $modelType_ ;

    public function __construct(string $modelType)
    {
        $this->modelType_ = $modelType;
    }

    public function getModel()
    {
        $model = null;
        switch ($this->modelType_) {
            case 'areas':
                $model = new App\Http\Controllers\Generics\Models\Area();
            break;
            case 'cities':
                $model = new App\Http\Controllers\Generics\Models\City();
            break;
            case 'job_types':
                $model = new  App\Http\Controllers\Generics\Models\JobType();
            break;
            case 'designations':
                $model = new  App\Http\Controllers\Generics\Models\Designation();
            break;
            case 'teams':
                $model = new  App\Http\Controllers\Generics\Models\Team();
            break;
            case 'locations':
                $model = new  App\Http\Controllers\Generics\Models\Location();
            break;
            case 'work_schedules':
                $model = new App\Http\Controllers\Generics\Models\WorkSchedule();
            break;
            case 'categories':
                $model = new App\Http\Controllers\Generics\Models\Category();
            break;
            case 'measurement_units':
                $model = new App\Http\Controllers\Generics\Models\MeasurementUnit();
            break;
            case 'usage_units':
                $model = new App\Http\Controllers\Generics\Models\UsageUnit();
            break;
            case 'asset_types':
                $model = new App\Http\Controllers\Generics\Models\AssetType();
            break;
            case 'customers':
                $model = new App\Http\Controllers\Generics\Models\Customer();
            break;
            case 'vat_codes':
                $model = new App\Http\Controllers\Generics\Models\VatCode();
            break;
        }

        return $model;
    }
}
