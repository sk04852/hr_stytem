<?php

namespace App\Http\Controllers\Estimates;

use App\Exceptions\LowStockException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Invoices\Requests\CalculateProductBillRequest;
use App\Http\Controllers\Estimates\Models\Estimate;
use App\Http\Controllers\Estimates\Requests\CreateEstimateRequest;
use App\Http\Controllers\Estimates\Requests\EstimateConversionRequest;
use App\Http\Controllers\Estimates\Requests\UpdateEstimateRequest;
use App\Http\Resources\Estimates\EstimateResource;
use App\Http\Resources\Estimates\EstimatesListingCollection;
use App\Http\Services\EstimatesService;
use App\Http\Services\InvoicesService;
use Exception;

class EstimatesController extends Controller
{
    const MODULE_NAME = 'estimate';
    const COLLECTION_NAME = 'estimates';
    const RECORD_FAILED = 'Estimate not created';
    const ORDER_CREATED = 'Estimate created successfully';
    const ORDER_UPDATED = 'Estimate updated successfully';
    const INVOICE_CREATED = 'Invoice created successfully';

    private $estimatesService_;
    private $invoicesService_;

    public function __construct(Estimate $model, EstimatesService $estimatesService, InvoicesService $invoicesService)
    {
        parent::__construct($model);
        $this->estimatesService_ = $estimatesService;
        $this->invoicesService_ = $invoicesService;
    }

    public function index()
    {
        try {
             $data = $this->model->where('company_id', $this->companyId())->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
             if ($data->isNotEmpty()) {
                return EstimatesListingCollection::collection($data);
             }
                return $this->created(['message' => EstimatesController::NO_RECORD, EstimatesController::COLLECTION_NAME=> $data], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function calculateProductPriceForEstimate(CalculateProductBillRequest $request){

        try{
            return $this->estimatesService_->calculateProductPrice($request);
        }catch(LowStockException $ex){
            return $ex->render();
        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

    public function store(CreateEstimateRequest $request){

        try{
            $result = $this->estimatesService_->storeEstimate($request, $this->companyId());
            if($result){
                return $this->created(['message'=> EstimatesController::ORDER_CREATED]);
            }
                return $this->created(['message'=> EstimatesController::RECORD_FAILED]);

        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

    public function show($id){
        try{
            $estimateDetail = Estimate::find($id);
            if($estimateDetail){
                return new EstimateResource($estimateDetail);
            }
            return $this->created(['message' => EstimatesController::RECORD_NOT_FOUND],200);
        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

    public function update(UpdateEstimateRequest $request){

        try{
            $estimateUpdated = $this->estimatesService_->updateEstimate($request);
            if($estimateUpdated){
                return $this->created(['message'=> EstimatesController::ORDER_UPDATED]);
            }
                return $this->created(['message'=> EstimatesController::RECORD_FAILED]);
        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

    public function estimateConversionToInvoice($id){

        try{
            $estimate = $this->model->find($id);
            if(!$estimate){
                return $this->created(['message' => EstimatesController::RECORD_NOT_FOUND],200);
            }else{
                $result = $this->invoicesService_->estimateConversion($estimate, $this->companyId(), $this->outletId(), $this->user());
                if($result == null){
                    return $this->created(['message'=> EstimatesController::INVOICE_CREATED]);
                } else {
                    return $this->created(['message'=> EstimatesController::RECORD_FAILED]);
                }
            }

        }catch(Exception $ex){
           return $this->serverError($ex);
        }
    }

}

