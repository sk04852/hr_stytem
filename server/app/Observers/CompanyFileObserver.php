<?php

namespace App\Observers;

use App\Http\Controllers\Companies\Models\CompanyFile;
use App\Http\Controllers\Companies\Models\Company;
use App\Traits\TimelineTrait;
use App\Models\History;
use App\Http\Controllers\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CompanyFileObserver
{
    /**
     * Handle the CompanyFile "created" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyFile  $companyFile
     * @return void
     */
    public function created(CompanyFile $companyFile)
    {
        $name = $companyFile->file_name;
            History::create([
            'reference_table' => 'company_attachments',
            'reference_id'    => $companyFile->company_pr_id,
            'actor_id'        => auth()->user()->id,
            'body'            => "File ${name} added",
        ]);
    }

    /**
     * Handle the CompanyFile "updated" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyFile  $companyFile
     * @return void
     */
    public function updated(CompanyFile $companyFile)
    {
        $this->track($companyFile, function ($value, $field) use ($companyFile) {
            $old = $companyFile->getOriginal(str_replace(' ', '_', $field));
            $name = $companyFile->file_name;
            $field = Str::ucfirst($field);
            return[
                'body' => "Company ${field} changed from {$old} to {$value}",
            ];
        }, null, $companyFile->company_pr_id);
    }

    /**
     * Handle the CompanyFile "deleted" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyFile  $companyFile
     * @return void
     */
    public function deleted(CompanyFile $companyFile)
    {
        $name = $companyFile->file_name;
            History::create([
            'reference_table' => 'company_attachments',
            'reference_id'    => $companyFile->company_pr_id,
            'actor_id'        => auth()->user()->id,
            'body'            => "File ${name} deleted",
        ]);
    }

    /**
     * Handle the CompanyFile "restored" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyFile  $companyFile
     * @return void
     */
    public function restored(CompanyFile $companyFile)
    {
        //
    }

    /**
     * Handle the CompanyFile "force deleted" event.
     *
     * @param  \App\Http\Controllers\Companies\Models\CompanyFile  $companyFile
     * @return void
     */
    public function forceDeleted(CompanyFile $companyFile)
    {
        $name = $companyFile->file_name;
            History::create([
            'reference_table' => 'company_attachments',
            'reference_id'    => $companyFile->company_pr_id,
            'actor_id'        => auth()->user()->id,
            'body'            => "File ${name} deleted",
        ]);
    }
}
