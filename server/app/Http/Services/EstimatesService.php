<?php
namespace App\Http\Services;

use App\Http\Controllers\Estimates\Models\Estimate;
use App\Http\Controllers\Estimates\Models\EstimateProduct;
use App\Http\Controllers\Companies\Models\CompanyGeneralSetting;
use Illuminate\Support\Facades\DB;

class EstimatesService {

   
    private $preOrderModel_;
    private $baseService_;
    private $companySettingsModel_;

    public function __construct(Estimate $preOrder, 
        BaseService $baseService,
        CompanyGeneralSetting $companySettingsModel) {
        $this->preOrderModel_ = $preOrder;
        $this->baseService_ = $baseService;
        $this->companySettingsModel_ = $companySettingsModel;
    }

    public function calculateProductPrice($request){
    
        return $this->baseService_->calculateProductPrice($request);
     }

    public function storeEstimate($request, $companyId){
        $estimateNo = $this->createEstimateNumber($companyId);
        $PreOrderData = [
           'estimate_number' => $estimateNo,
           'estimate_date' => $request->estimate_date,
           'expiry_date' => $request->expiry_date,
           'sales_person' => $request->sales_person,
           'subject' => $request->subject,
           'notes' => $request->notes,
           'terms' => $request->terms,
           'status' => $request->status,
           'sub_total' => $request->sub_total,
           'discount' => $request->discount,
           'discount' => $request->has('discount', 0),
           'shipping_charges' => $request->has('shipping_charges', 0),
           'status' => $request->has('status', 'Pending'),
           'balance' => $request->has('balance', 0),
           'amount_paid' => $request->amount_paid,
           'client_id' => $request->client_id,

           'company_id' =>  auth()->user()->company_id,
        ];
           $estimatedCreated = $this->preOrderModel_->create($PreOrderData);
           if($estimatedCreated){
                $estimateProducts = [];
                $products = $request->products;
                foreach ($products as $key => $invoiceProduct) {
                    $invoiceProduct['invoice_id'] = $estimatedCreated->id;
                    $estimateProducts[] = $invoiceProduct;
                }    
                $invoiceDone = EstimateProduct::insert($estimateProducts);
                if($invoiceDone){
                    return true;
                }else{
                    return false;
                }
           }else{
                return false;
           }
    }

    public function createEstimateNumber($companyId)
    {
        $nextInvoiceNumber = null;
        $invoicePrefix = "EST";
        $separator = "-";
        $lastEstimate = DB::table('estimates')->where('company_id', $companyId)->latest('estimate_number')->first();
        $companySettings = $this->companySettingsModel_->where('company_id', $companyId)->select('prefix_seperator','invoice_prefix')->first();
        if (!$lastEstimate) {
            $nextInvoiceNumber = 1;
        }else {
            if($companySettings) {
                $separator = (!empty($companySettings->prefix_seperator))? $companySettings->prefix_seperator: "";
                $invoicePrefix = (!empty($companySettings->invoice_prefix))? $companySettings->invoice_prefix: "";
            }
            if($lastEstimate) {
                $splits = explode($lastEstimate->estimate_number, $separator);
                if(count($splits) == 1) {
                    $nextInvoiceNumber = (int)$lastEstimate->estimate_number;
                } else {
                    $nextInvoiceNumber = (int)$splits[count($splits)-1];
                }
                $nextInvoiceNumber+=1;
            }
        }
        return $invoicePrefix.$separator.$nextInvoiceNumber;
    }

    public function updateEstimate($request){
    
        $estimateData = [
          'estimate_date' => $request->estimate_date,
          'expiry_date' => $request->expiry_date,
           'sales_person' => $request->sales_person,
           'subject' => $request->subject,
           'notes' => $request->notes,
           'terms' => $request->terms,
           'status' => $request->status,
           'sub_total' => $request->sub_total,
           'discount' => $request->discount,
           'shipping_charges' => $request->has('shipping_charges', 0),
           'balance' => $request->balance,
           'amount_paid' => $request->amount_paid,
           'client_id' => $request->client_id,
           'company_id' =>  auth()->user()->company_id,

        ];
        $result = Estimate::where('id', $request->estimate_id)->update($estimateData);
        if($result){
           $orders = [];
           $preOrderIds = $request->pre_order_product_id;
           foreach ($preOrderIds as $key => $value) {
                $orders = [
                        'product_id' => $request->product_id[$key],
                        'product_name' => $request->product_name[$key],
                        'price' =>  $request->price[$key],
                        'quantity' => $request->quantity[$key],
                        'discount' => $request->discount[$key],
                        'total' => $request->total[$key],
                        'sub_total' => $request->sub_total[$key],
                ];
                EstimateProduct::where('id', $value)->update($orders);
           }
           return true; 
        }else{
            return false;
        }   
    }

}

?>