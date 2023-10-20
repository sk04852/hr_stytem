<?php namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Models\Setting;

class SettingsController extends Controller{
    
    private $model_;
    public function __construct(Setting $model){
        $this->model_ = $model;
    }

    public function fetchAll(){
        return $this->model_->where('mode',env("APP_ENV"))->orWhere('mode','all')->get();
    }

    public function get(string $settingName){
        return $this->model_->where(function($query) use ($settingName){
                                    $query->where('mode',env("APP_ENV"));
                                    $query->orWhere('mode','all');
                                   })->Where('name',$settingName)->first();
    }
}