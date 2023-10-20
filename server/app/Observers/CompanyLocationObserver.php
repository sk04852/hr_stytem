<?php

namespace App\Observers;

use App\Http\Controllers\Companies\Models\CompanyLocation;
use App\Http\Controllers\Companies\Models\Company;
use App\Traits\TimelineTrait;
use App\Models\History;
use App\Http\Controllers\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CompanyLocationObserver
{
    /**
     * Handle the CompanyLocation "created" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyLocation  $companyLocation
     * @return void
     */
    public function created(CompanyLocation $companyLocation)
    {
        $name = $companyLocation->location;
            History::create([
            'reference_table' => 'company_locations',
            'reference_id'    => $companyLocation->company_pr_id,
            'actor_id'        => auth()->user()->id,
            'body'            => "Company Location ${name} added",
        ]);
    }

    /**
     * Handle the CompanyLocation "updated" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyLocation  $companyLocation
     * @return void
     */
    public function updated(CompanyLocation $companyLocation)
    {
        $this->track($companyLocation, function ($value, $field) use ($companyLocation) {
            $old = $companyLocation->getOriginal(str_replace(' ', '_', $field));
            $name = $companyLocation->name;
            $field = Str::ucfirst($field);
            return[
                'body' => "Company ${field} changed from {$old} to {$value}",
            ];
        }, null, $companyLocation->company_pr_id);
    }

    /**
     * Handle the CompanyLocation "deleted" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyLocation  $companyLocation
     * @return void
     */
    public function deleted(CompanyLocation $companyLocation)
    {
        $name = $companyLocation->getOriginal('location');
            History::create([
            'reference_table' => 'company_locations',
            'reference_id'    => $companyLocation->company_pr_id,
            'actor_id'        => auth()->user()->id,
            'body'            => "Company Location ${name} deleted",
        ]);
    }

    /**
     * Handle the CompanyLocation "restored" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyLocation  $companyLocation
     * @return void
     */
    public function restored(CompanyLocation $companyLocation)
    {
        //
    }

    /**
     * Handle the CompanyLocation "force deleted" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyLocation  $companyLocation
     * @return void
     */
    public function forceDeleted(CompanyLocation $companyLocation)
    {
        //
    }
}
