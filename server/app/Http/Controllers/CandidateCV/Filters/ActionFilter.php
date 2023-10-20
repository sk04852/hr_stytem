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

class ActionFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->join('candidatecv_jobs AS cj', function($query) use ($value){
            $query->on('candidatecv.id', '=', 'cj.candidatecv_id');
            $query->where('cj.action_id', '=', $value);
        });
        return $query;
    }
}
