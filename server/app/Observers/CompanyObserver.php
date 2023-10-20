<?php

namespace App\Observers;

use App\Http\Controllers\Companies\Models\Company;
use App\Traits\TimelineTrait;
use App\Models\History;
use App\Http\Controllers\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CompanyObserver
{
    use TimelineTrait;
    /**
     * Handle the Company "created" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\Company  $company
     * @return void
     */
    public function created(Company $company)
    {
        $name = $company->name;
            History::create([
            'reference_table' => 'companies',
            'reference_id'    => $company->company_pr_id,
            'actor_id'        => auth()->user()->id,
            'body'            => "Company ${name} created",
        ]);
    }

    /**
     * Handle the Company "updated" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\Company  $company
     * @return void
     */
    public function updated(Company $company)
    {
        $this->track($company, function ($value, $field) use ($company) {
            $old = $company->getOriginal(str_replace(' ', '_', $field));
            $field = Str::ucfirst($field);
            return[
                'body' => "{$field} changed from {$old} to {$value}",
            ];
        }, null, $company->company_pr_id);
    }

    /**
     * Handle the Company "deleted" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\Company  $company
     * @return void
     */
    public function deleted(Company $company)
    {
        //
    }

    /**
     * Handle the Company "restored" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\Company  $company
     * @return void
     */
    public function restored(Company $company)
    {
        //
    }

    /**
     * Handle the Company "force deleted" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\Company  $company
     * @return void
     */
    public function forceDeleted(Company $company)
    {
        //
    }
}
