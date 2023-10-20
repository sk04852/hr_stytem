<?php
namespace App\Http\Controllers\ImportData;

use App\Http\Builders\ImportCollectionBuilder;
use Exception;
use App\Http\Services\MediaService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\ImportData\Models\CsvData;
use App\Http\Controllers\ImportData\Requests\ImportDataRequest;
use App\Http\Controllers\ImportData\Requests\ProcessImportRequest;

abstract class Importer {
    protected $mediaService_;

    public function __construct(MediaService $mediaService)
    {
       $this->mediaService_ = $mediaService;
    }

    public function upload(ImportDataRequest $request) {
        try {
            $uploadedMedia = $this->mediaService_->saveMedia('file', $request);
            $header = $request->get('header', null);
            if(is_null($uploadedMedia)) {
                throw new Exception("There was a problem uploading file, please try again.");
            }
            $parsedData = $this->parseImport($uploadedMedia, $header, $request->module_id);
            return $parsedData;
        } catch(Exception $ex) {

        }
    }

    public function parseImport($uploadedMedia, $header, $moduleId)
    {
        $uploadedFileNameWithPath = $this->mediaService_->getFilePath($uploadedMedia['media']['file_name'],"",false);
        $csv_data_file = null;
        if (!is_null($header)) {
            $data = Excel::load($uploadedFileNameWithPath, function($reader) {})->get()->toArray();
        } else {
            $data = array_map('str_getcsv', file($uploadedFileNameWithPath));
        }
        if (count($data) > 0) {
            if (!is_null($header)) {
                $csv_header_fields = [];
                foreach ($data[0] as $key => $value) {
                    $csv_header_fields[] = $key;
                }
            } else {
                if($moduleId == null)
                throw new Exception("Unable to find the module id, operation failed");
                $importCollectionBuilder = new ImportCollectionBuilder($moduleId);
                $importCollection = $importCollectionBuilder->build();
                $csv_header_fields = $importCollection->getFillable();
            }

            $csv_data = array_slice($data, 0, 2);
            $csv_data_file = CsvData::create([
                'csv_filename' => $uploadedMedia['media']['file_name'],
                'csv_header' => !is_null($header),
                'csv_header_fields'=> json_encode($csv_header_fields),
                'csv_data' => json_encode($csv_data)
            ]);
        }
        return $csv_data_file;
    }

    public function processImport(ProcessImportRequest $request) {
        $data = CsvData::find($request->file_id);
        $importCollectionBuilder = new ImportCollectionBuilder($request->module_id);
        $importCollection = $importCollectionBuilder->build();
        if(is_null($data))
            throw new Exception("Data file not found, operation failed");

        if(!empty($data['csv_filename'])) {
            $uploadedFileNameWithPath = $this->mediaService_->getFilePath($data['csv_filename'],"",false);
            $data = Excel::toArray($importCollection, $uploadedFileNameWithPath, null, null);
            $header = $request->header;

            if(isset($data[0])) {
                $importCollection->process($header, $data);
            }
        }
        return true;
    }

}

?>
