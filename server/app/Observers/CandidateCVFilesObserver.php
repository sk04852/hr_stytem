<?php

namespace App\Observers;

use App\Traits\TimelineTrait;
use App\Http\Controllers\CandidateCV\Models\CandidateCVFiles;
use Illuminate\Support\Facades\DB;
use App\Models\History;
use App\Http\Controllers\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CandidateCVFilesObserver
{
    use TimelineTrait;
    /**
     * Handle the CandidateCVFiles "created" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVFiles  $candidateCVFiles
     * @return void
     */
    public function created(CandidateCVFiles $candidateCVFiles)
    {
//        $name = $candidateCVFiles->file_name;
//        History::create([
//            'reference_table' => 'candidatecv_files',
//            'reference_id'    => $candidateCVFiles->candidatecv_id,
//            'actor_id'        => auth()->user()->id,
//            'body'            => "File {$name} added",
//        ]);
    }

    /**
     * Handle the CandidateCVFiles "updated" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVFiles  $candidateCVFiles
     * @return void
     */
    public function updated(CandidateCVFiles $candidateCVFiles)
    {
        //
    }

    /**
     * Handle the CandidateCVFiles "deleted" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVFiles  $candidateCVFiles
     * @return void
     */
    public function deleted(CandidateCVFiles $candidateCVFiles)
    {
        //
    }

    /**
     * Handle the CandidateCVFiles "restored" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVFiles  $candidateCVFiles
     * @return void
     */
    public function restored(CandidateCVFiles $candidateCVFiles)
    {
        //
    }

    /**
     * Handle the CandidateCVFiles "force deleted" event.
     *
     * @param  \App\Http\Controllers\CandidateCV\Models\CandidateCVFiles  $candidateCVFiles
     * @return void
     */
    public function forceDeleted(CandidateCVFiles $candidateCVFiles)
    {
        //
    }
}
