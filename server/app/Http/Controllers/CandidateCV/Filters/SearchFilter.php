<?php

namespace App\Http\Controllers\CandidateCV\Filters;

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
        // $query->join('user_pr', 'candidatecv.user_id', '=', 'user_pr.id');
        // $query->join('users', 'user_pr.user_ID', '=', 'users.id');
        // $query->orWhere('first_name', 'like', "%$value%");
        // $query->orWhere('last_name', 'like', "%$value%");
        // $query->orWhere('gender', 'like', '%'.Str::lower($value).'%');
        // $query->orWhere('candidatecv.phone', 'like', "%$value%");
        // $query->orWhere('candidatecv.location', 'like', "%$value%");
        // $query->orWhere('users.name', 'like', "%$value%");
//        $query->orWhere('job_type', 'like', '%'.Str::lower(str_replace(' ', '_', $value)).'%');
        $query->where(function($query) use ($value){
            $query->orWhere('first_name', 'like', "%$value%");
            $query->orWhere('last_name', 'like', "%$value%");
            $query->orWhere('candidatecv.phone', 'like', "%$value%");
            $query->orWhere('candidatecv.location', 'like', "%$value%");
            $query->orWhere('candidatecv.email', 'like', "%$value%");
        });
        return $query;
    }
}
