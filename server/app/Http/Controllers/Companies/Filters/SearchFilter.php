<?php

namespace App\Http\Controllers\Companies\Filters;

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
        $query->where(function($query) use ($value){
            $query->orWhere('c.name', 'like', "%$value%");
        });
        return $query;
    }
}
