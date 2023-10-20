<?php
namespace App\Http\Controllers\ImportData\Models;

use App\Models\BaseModel;
class CsvData extends BaseModel
{
    protected $table = 'csv_data';
    protected $fillable = ['csv_filename', 'csv_header', 'csv_data', 'csv_header_fields'];
}

?>
