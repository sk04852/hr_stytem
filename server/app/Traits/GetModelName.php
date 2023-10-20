<?php

namespace App\Traits;

trait GetModelName
{
    public function getModelName($type) {
        
        $tableName = '';
        switch($type){
            case "measurement_units":
                $tableName = 'measurement_unit';
            break;
            case "usage_units":
                $tableName = 'usage_unit';
            break;
            case "areas":
                $tableName = 'area';
            break;
            case "cities":
                $tableName = 'citiy';
            break;
            case "categories":
                $tableName = 'category';
            break;
            case "designations":
                $tableName = 'designation';
            break;
            case "job_types":
                $tableName = 'job_type';
            break;
            case "asset_types":
                $tableName = 'asset type';
            break;
            case "teams":
                $tableName = 'team';
            break;
            case "locations":
                $tableName = 'location';
            break;
            case "work_schedules":
                $tableName = 'work schedule';
            break;
            case "customers":
                $tableName = 'customer';
            break;
            case "vat_codes":
                $tableName = 'vat code';
            break;
        }

        return $tableName;
    }   
}
?>