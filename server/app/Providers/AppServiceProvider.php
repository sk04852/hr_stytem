<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'local') {
            //$this->app->register(\Reliese\Coders\CodersServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::defaultStringLength(191); // Update defaultStringLength
        Relation::morphMap([
            'Products' => 'App\Http\Controllers\Products\Models\Product',
            'Services' => 'App\Http\Controllers\Services\Models\Service',
            'CalendarEvent' => 'App\Http\Controllers\CalendarEvents\Models\CalendarEvent',
            'Client' => 'App\Http\Controllers\Clients\Models\Client',
            'User' => 'App\Http\Controllers\Users\Models\User',
            'Employee' => 'App\Http\Controllers\EmsEmployee\Models\Employee',
        ]);
    }
}
