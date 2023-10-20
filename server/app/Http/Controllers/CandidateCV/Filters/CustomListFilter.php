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

class CustomListFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->join('custom_candidate_lists AS ccl', function($query) use ($value){
            $query->on('candidatecv.id', '=', 'ccl.candidatecv_id');
            $query->where('ccl.list_name_id', '=', $value);
        });
        return $query;
    }
}
