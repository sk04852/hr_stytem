<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;


class TestImport implements ToCollection
{
    public function collection(Collection $collection)
    {
        return $collection;
    }
}
