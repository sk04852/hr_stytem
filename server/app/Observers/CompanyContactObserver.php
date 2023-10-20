<?php

namespace App\Observers;

use App\Http\Controllers\Companies\Models\CompanyContact;
use App\Http\Controllers\Companies\Models\Company;
use App\Traits\TimelineTrait;
use App\Models\History;
use App\Http\Controllers\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CompanyContactObserver
{   
    use TimelineTrait;
    /**
     * Handle the CompanyContact "created" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyContact  $companyContact
     * @return void
     */
    public function created(CompanyContact $companyContact)
    {
        $name = $companyContact->name;
            History::create([
            'reference_table' => 'company_contacts',
            'reference_id'    => $companyContact->company_pr_id,
            'actor_id'        => auth()->user()->id,
            'body'            => "Company Contact ${name} created",
        ]);
    }

    /**
     * Handle the CompanyContact "updated" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyContact  $companyContact
     * @return void
     */
    public function updated(CompanyContact $companyContact)
    {
        $this->track($companyContact, function ($value, $field) use ($companyContact) {
            $old = $companyContact->getOriginal(str_replace(' ', '_', $field));
            $name = $companyContact->name;
            $field = Str::ucfirst($field);
            return[
                'body' => "Company Contact ${name}'s {$field} changed from {$old} to {$value}",
            ];
        }, null, $companyContact->company_pr_id);
    }

    /**
     * Handle the CompanyContact "deleted" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyContact  $companyContact
     * @return void
     */
    public function deleted(CompanyContact $companyContact)
    {
        $name = $companyContact->name;
            History::create([
            'reference_table' => 'company_contacts',
            'reference_id'    => $companyContact->company_pr_id,
            'actor_id'        => auth()->user()->id,
            'body'            => "Company Contact ${name} deleted",
        ]);
    }

    /**
     * Handle the CompanyContact "restored" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyContact  $companyContact
     * @return void
     */
    public function restored(CompanyContact $companyContact)
    {
        //
    }

    /**
     * Handle the CompanyContact "force deleted" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyContact  $companyContact
     * @return void
     */
    public function forceDeleted(CompanyContact $companyContact)
    {
        //
    }
}
