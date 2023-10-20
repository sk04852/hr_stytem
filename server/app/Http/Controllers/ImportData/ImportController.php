<?php

namespace App\Http\Controllers\ImportData;

use App\Http\Controllers\ImportData\DataImporter;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ImportData\Requests\ImportDataRequest;
use App\Http\Controllers\ImportData\Requests\ProcessImportRequest;
use App\Models\BaseModel;
use Exception;

class ImportController extends Controller
{

    private $dataImporter_;

    public function __construct(DataImporter $dataImporter)
    {
        parent::__construct(new BaseModel());
        $this->dataImporter_ = $dataImporter;
    }

    public function parseImport(ImportDataRequest $request) {
        try {
            return $this->dataImporter_->upload($request);
        } catch(Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function processImport(ProcessImportRequest $request) {
        try {
            $this->dataImporter_->processImport($request);
            return $this->created(['message'=> "Import completed"]);
        } catch(Exception $ex) {
            return $this->serverError($ex);
        }
    }

}
