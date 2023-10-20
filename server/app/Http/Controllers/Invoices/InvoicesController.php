<?php

namespace App\Http\Controllers\Invoices;

use App\Exceptions\LowStockException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Invoices\Models\Invoice as ThisModel;
use App\Http\Controllers\Invoices\Requests\CalculateProductBillRequest;
use App\Http\Controllers\Invoices\Requests\CreateInvoiceRequest;
use App\Http\Controllers\Invoices\Requests\UpdateInvoiceRequest;
use App\Http\Resources\Invoices\InvoiceResource;
use App\Http\Resources\Invoices\Listing\InvoicesListingCollection;
use App\Http\Services\InvoicesService;
use App\Http\Controllers\Modules\Enums\MediaTypesEnum;
use App\Http\Services\MediaService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Exception;

class InvoicesController extends Controller
{
    const MODULE_NAME = 'invoice';
    const COLLECTION_NAME = 'invoices';
    const RECORD_FAILED = 'Invoice not created';
    const INVOICE_CREATED = 'Invoice created successfully';
    const INVOICE_UPDATED = 'Invoice updated successfully';
    const INVOICE_SENT = 'Invoice successfully sent to client';

    private $invoicesService_;
    private $mediaService_;

    public function __construct(
        ThisModel $model,
        MediaService $mediaService,
        InvoicesService $invoicesService)
    {
        parent::__construct($model);
        $this->invoicesService_ = $invoicesService;
        $this->mediaService_ = $mediaService;
    }

    public function index()
    {
        try {
            $data = ThisModel::where('company_id', $this->companyId())->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            return InvoicesListingCollection::collection($data);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function calculateProductPriceForInvoice(CalculateProductBillRequest $request){

        try{
            return $this->invoicesService_->calculateProductPrice($request);
        }catch(LowStockException $ex){
            return $ex->render();
        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

    public function store(CreateInvoiceRequest $request){
        try{
            $result = $this->invoicesService_->storeInvoice($request, $this->companyId(), $this->outletId(), $this->user());
            if($result == null){
                return $this->created(['message'=> InvoicesController::INVOICE_CREATED]);
            } else {
                return $this->created(['message'=> InvoicesController::RECORD_FAILED]);
            }
        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

    public function show($id){
        try{
            $singleInvoiceData = ThisModel::where('id', $id)->orWhere('invoice_number', $id)->first();
            if($singleInvoiceData){
                return new InvoiceResource($singleInvoiceData);
            }
            return $this->created(['message' => InvoicesController::RECORD_NOT_FOUND],200);
        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

    public function update(UpdateInvoiceRequest $request){
        try{
            $invoiceUpdated = $this->invoicesService_->updateInvoice($request);
            if($invoiceUpdated){
                return $this->created(['message'=> InvoicesController::INVOICE_UPDATED]);
            }
                return $this->created(['message'=> InvoicesController::RECORD_FAILED]);
        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

    public function sendInvoiceViaEmail(int $invoiceId, int $clientId){
        try{
            $isSent = $this->invoicesService_->sendInvoice($invoiceId);
            if($isSent){
                return $this->created(['message'=> InvoicesController::INVOICE_SENT]);
            }
            return $this->created(['message' => InvoicesController::RECORD_NOT_FOUND],200);
        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

    public function generatePDF(int $invoiceId) {
        try{
             $isGenerate = $this->invoicesService_->createPDF($invoiceId);
             if($isGenerate){
                 return $isGenerate;
             }else{
                 return $this->created(['message' => InvoicesController::RECORD_NOT_FOUND],200);
             }
        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

    public function generateInvoiceCode() {
        return $this->created(['invoice_number'=> $this->invoicesService_->createInvoiceNumber($this->companyId(), $this->outletId())]);
    }

    public function getFiles(int $invoiceId) {
        return $this->invoicesService_->getInvoiceFiles($invoiceId);
    }

    public function downloadInvoicePDF(int $invoiceId) {
        $files = $this->getFiles($invoiceId);
        if($files->count() === 0) {
            echo 'Invoice not found'; // Fix Me: Should redirect.
        } else {
            $pdfFiles = $files->filter(function($file) {
                return $file->media_type === MediaTypesEnum::InvoicesPDF;
            });
            if($pdfFiles->count() > 0) {
                $fileNameWithPath = $this->mediaService_->getPath(null, true).$pdfFiles[0]->file_name;
                $file = File::get($fileNameWithPath);
                $type = File::mimeType($fileNameWithPath);
                $response = Response::make($file, 200);
                $response->header("Content-Type", $type);
                return $response;
            }
        }
    }
}

