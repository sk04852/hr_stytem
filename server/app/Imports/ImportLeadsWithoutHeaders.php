<?php

namespace App\Imports;

use App\Http\Controllers\Leads\Models\Lead;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportLeadsWithoutHeaders implements ToModel
{
   /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Lead([
                    'brand_id' => $row[0] ?? $row[0],
                    'lead_id' => $row[1] ?? $row[1],
                    'title' => $row[2] ?? $row[2],
                    'first_name' => $row[3] ?? $row[3],
                    'last_name' => $row[4] ?? $row[4],
                    'primary_phone' => $row[5] ?? $row[5],
                    'secondary_phone' => $row[6] ?? $row[6],
                    'mobile' => $row[7] ?? $row[7],
                    'city' => $row[8] ?? $row[8],
                    'country' => $row[9] ?? $row[9],
                    'lead_source' => $row[10] ?? $row[10],
                    'primary_email' => $row[11] ?? $row[11],
                    'secondary_email' => $row[12] ?? $row[12],
                    'assigned_to' => $row[13] ?? $row[13],
                    'lead_supplier' => $row[14] ?? $row[14],
                    'lead_status' => $row[15] ?? $row[15],
                    'promo_code' => $row[16] ?? $row[16],
                    'created_by' => $row[17] ?? $row[17],
        ]);
    }
}
