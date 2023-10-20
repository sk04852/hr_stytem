<?php

namespace App\Http\Controllers\Leads;

use App\Exports\LeadsExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Leads\Requests\ExportLeadRequest;
use App\Http\Controllers\Leads\Requests\FileRequest;
use App\Imports\ImportLeads;
use App\Imports\ImportLeadsWithHeaders;
use App\Imports\ImportLeadsWithoutHeaders;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class BulkUploadLeads extends Controller
{
    public function __construct() {

    }

    public function uploadFile(FileRequest $request){
        try {
            return $this->GetColumns($request);

       } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    public function message($message){
        return $this->created(['message' => $message]);
    }

    public function fileHeaders(FileRequest $request){
        try {

            if ($request->has_headers == 0) {
                Excel::import(new ImportLeadsWithoutHeaders,request()->file('csv_file'));
                return response()->json(['message' => 'Leads Imported Successfully']);
            }
            $file = $request->file('csv_file');
            $import = new ImportLeads();
            $import->import($file);
            $leadsValidationErrors = $import->getErrors();
            if (count($leadsValidationErrors)) {
                return response()->json(['errors' => $leadsValidationErrors], 422);
            }
            return response()->json(['message' => 'Data Imported Successfully']);

       } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function exportLeads()
    {
        return Excel::download(new LeadsExport(), 'leads.csv');
    }

    public function GetColumns($request){

        $filename = request()->file('csv_file');
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            $counter =0 ;
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE)
            {
               if ($counter == 1) {
                  break;
               }else{
                   return  $row ;
            }
            $counter ++;
            }
        }
    }


}
