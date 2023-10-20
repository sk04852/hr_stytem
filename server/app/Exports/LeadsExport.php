<?php

namespace App\Exports;

use App\Http\Controllers\Leads\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;

class LeadsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Lead::all();
    }
}
