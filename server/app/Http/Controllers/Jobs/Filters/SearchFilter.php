<?php

namespace App\Http\Controllers\Jobs\Filters;

/**
 * SearchFilter
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SearchFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        // $query->join('companies_pr', function ($query){
        //     $query->on('jobs_pr.company_pr_id', '=', 'companies_pr.id');
        //     $query->whereNull('companies_pr.deleted_at');
        // });

        // $query->join('companies', function ($query){
        //     $query->on('companies_pr.id', '=', 'companies.company_pr_id');
        //     $query->where('companies.locale', app()->getLocale());
        //     $query->whereNull('companies.deleted_at');
        // });

        // $query->join('jobs', function ($query){
        //     $query->on('jobs_pr.id', '=', 'jobs.job_pr_id');
        //     $query->whereNull('jobs.deleted_at');
        // });

        // $query->orWhere('j.offer_name', 'like', "%$value%");
        // $query->orWhere('j.location', 'like', "%$value%");
        // $query->orWhere('c.name', 'like', '%'.Str::lower($value).'%');
        // $query->orWhere('jobs_pr.job_type', 'like', "%$value%");

        //        $query->orWhere('job_type', 'like', '%'.Str::lower(str_replace(' ', '_', $value)).'%');

        $query->where(function ($query) use ($value) {
            $query->orWhere('j.offer_name', 'like', "%$value%");
            $query->orWhere('j.location', 'like', "%$value%");
            $query->orWhere('c.name', 'like', '%' . Str::lower($value) . '%');
            $query->orWhere('jobs_pr.job_type', 'like', "%$value%");
        });

        return $query;
    }
}
