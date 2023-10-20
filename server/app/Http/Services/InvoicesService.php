<?php
namespace App\Http\Services;

use App\Models\Module;
use Illuminate\Support\Str;
use App\Events\InvoiceEmailEvent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Modules\Enums\ModuleEnum;
use App\Http\Services\CommentService;
use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Companies\Models\CompanyGeneralSetting;
use App\Http\Controllers\DriversInvoices\Models\DriverInvoice;
use App\Http\Controllers\Outlets\Models\Outlet;
use App\Http\Controllers\Invoices\Models\Invoice;
use App\Http\Controllers\Invoices\Models\InvoiceProduct;
use App\Http\Controllers\Modules\Enums\SaleTransactionTypeEnum;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Modules\Enums\MediaTypesEnum;
use App\Jobs\GenerateInvoicePDFJob;
use Barryvdh\DomPDF\Facade as PDF;

class InvoicesService {


    private $invoiceModel_;
    private $baseService_;
    private $accountingService_;
    private $outletModel_;
    private $clientEntity_;
    private $clientsService_;
    private $commentService_;
    private $mediaService_;

    public function __construct(Invoice $invoice,
                                BaseService $baseService,
                                Outlet $outletModel,
                                Client $clientEntity,
                                ClientsService $clientsService,
                                CommentService $commentService,
                                MediaService $mediaService,
                                AccountingService $accountingService) {

        $this->invoiceModel_ = $invoice;
        $this->baseService_ = $baseService;
        $this->outletModel_ = $outletModel;
        $this->clientEntity_ = $clientEntity;
        $this->accountingService_ = $accountingService;
        $this->clientsService_ = $clientsService;
        $this->commentService_ = $commentService;
        $this->mediaService_ = $mediaService;
    }

    public function calculateProductPrice($request){

       return $this->baseService_->calculateProductPrice($request);
    }

    public function createInvoiceNumber(int $companyId, int $outletId)
    {
        $nextInvoiceNumber = null;
        $invoicePrefix = "INV";
        $separator = "-";
        $lastInvoice = DB::table('invoices')->where('company_id', $companyId)->latest('id')->first();
        $outlet = $this->outletModel_->where('id', $outletId)->with('settings')->first();

        if($outlet) {
            $separator = (!empty($outlet->settings->invoice_prefix_separator))? $outlet->settings->invoice_prefix_separator: $separator;
            $invoicePrefix = (!empty($outlet->settings->invoice_prefix))? $outlet->settings->invoice_prefix: $invoicePrefix;
        }

        if (!$lastInvoice) {
            $nextInvoiceNumber = 1;
        }else {
                $splits = explode($separator, $lastInvoice->invoice_number);
                if(count($splits) == 1) {
                    $nextInvoiceNumber = (int)$lastInvoice->invoice_number;
                } else {
                    $nextInvoiceNumber = (int)$splits[count($splits)-1];
                }
                $nextInvoiceNumber+=1;
        }
        return $invoicePrefix.$separator.$nextInvoiceNumber;
    }

    public function storeInvoice($request, int $companyId, int $outletId, User $user){
        $invoiceNo = (!empty($request->invoice_number))? $request->invoice_number: $this->createInvoiceNumber($companyId, $outletId);
        // saving invoice personal data
        $InvoicePersonalData = [
           'payment_type' => $request->payment_type,
           'invoice_number' => $invoiceNo,
           'order_no' => $request->order_no,
           'invoice_date' => $request->invoice_date,
           'due_date' => $request->due_date,
           'sales_person' => $request->sales_person,
           'subject' => $request->subject,
           'customer_note' => $request->customer_note,
           'terms_n_conditions' => $request->terms_n_conditions,
           'status' => ($request->already_paid != null && $request->already_paid === true)? 'Paid': $request->status,
           'sub_total' => $request->sub_total,
           'total' => $request->total,
           'tax' => $request->tax,
           'discount' => (isset($request->discount))? $request->discount: 0,
           'shipping_charges' => (isset($request->shipping_charged))? $request->shipping_charged: 0,
           'balance' => $request->balance,
           'amount_paid' => $request->amount_paid,
           'client_id' => $request->client_id,
           'company_id' =>  $companyId,
        ];
        DB::transaction(function () use($InvoicePersonalData, $request, $companyId, $outletId, $user) {
            $invoice = $this->invoiceModel_->create($InvoicePersonalData);
            if($invoice){
                $invoiceProducts = [];
                $products = $request->products;
                foreach ($products as $key => $invoiceProduct) {
                    $invoiceProduct['invoice_id'] = $invoice->id;
                    $invoiceProduct['vat_percentage'] = (is_null($invoiceProduct['vat_percentage']))? '0': $invoiceProduct['vat_percentage'];
                    $invoiceProducts[] = $invoiceProduct;
                }
                $productsInserted = InvoiceProduct::insert($invoiceProducts);

                if(!$productsInserted){
                    return false;
                }else{
                    $assignInvoiceToDriver = CompanyGeneralSetting::where('company_id', $companyId)->where('assign_invoice_to_driver', 1)
                                           ->select(['assign_invoice_to_driver'])->first();
                    if($assignInvoiceToDriver){
                        DriverInvoice::create([
                            'driver_id' => $user->id,
                            'invoice_id' => $invoice->id
                        ]);
                    }
                    $invoiceModule = Module::find(getModuleIdFromEntity($this->invoiceModel_));
                    $client = $this->clientEntity_->find($request->client_id);
                    $saleTransactionType = ($request->type === 'Credit') ? SaleTransactionTypeEnum::CreditSale: SaleTransactionTypeEnum::CashSale;
                    if($saleTransactionType == SaleTransactionTypeEnum::CreditSale && $assignInvoiceToDriver) {
                        $saleTransactionType = SaleTransactionTypeEnum::EmployeeReceivable;
                    }
                    $this->accountingService_->record(
                                                    $companyId,
                                                    $saleTransactionType,
                                                    $invoiceModule,
                                                    $invoice,
                                                    $this->getInvoiceTransactionId($companyId, $outletId, $invoice->invoice_number),
                                                    $invoice->total
                                                );
                    if($client && $saleTransactionType == SaleTransactionTypeEnum::CreditSale) {
                        $this->clientsService_->addClientReceivables($client->id, $invoice->total);
                    }

                    if($invoice->client_id > 0) {
                        $this->createInvoiceCommentForClient($user, $invoice);
                    }

                    $job = (new GenerateInvoicePDFJob($invoice->id))->delay(now()->addSeconds(1));
                    dispatch($job);
                    return true;
                }

            }else{
                return false;
            }
        });
    }

    private function getInvoiceTransactionId(int $companyId, int $outletId, string $invoiceNumber) {
        return $companyId . "-" . $outletId . "-" . $invoiceNumber;
    }

    public function estimateConversion($estimate, int $companyId, int $outletId, User $user){
        $invoiceNo = $this->createInvoiceNumber($companyId, $outletId);
        // saving invoice personal data
        $InvoicePersonalData = [
           'payment_type' => 'Cash',
           'invoice_number' => $invoiceNo,
           'order_no' => $estimate->estimate_number.'_'.$invoiceNo,
           'invoice_date' => date("Y-m-d"),
           'due_date' => date("Y-m-d"),
           'sales_person' => $companyId,
           'subject' => $estimate->subject,
           'customer_note' => $estimate->notes,
           'terms_n_conditions' => $estimate->terms,
           'status' => $estimate->status,
           'sub_total' => $estimate->sub_total,
           'total' => $estimate->total,
           'discount' => $estimate->discount,
           'shipping_charges' => $estimate->shipping_charges,
           'balance' => $estimate->balance,
           'amount_paid' => $estimate->amount_paid,
           'client_id' => $estimate->client_id,
           'company_id' =>  $companyId,
        ];
        DB::transaction(function () use($InvoicePersonalData, $estimate, $companyId, $outletId, $user) {
            $invoice = $this->invoiceModel_->create($InvoicePersonalData);
            if($invoice){
                $invoiceProducts = [];
                $products = $estimate->products;

                foreach ($products as $key => $invoiceProduct) {
                    $invoiceProducts[$key] = [
                        "invoice_id"=> $invoice->id,
                        "vat_percentage" => $invoiceProduct->vat_percentage,
                        "product_name" => $invoiceProduct->product_name,
                        "price" => $invoiceProduct->price,
                        "quantity" => $invoiceProduct->quantity,
                        "total" => $invoiceProduct->total,
                        "sub_total" => $invoiceProduct->sub_total,
                        "discount" => $invoiceProduct->discount,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s")
                    ];
                }
                $productsInserted = InvoiceProduct::insert($invoiceProducts);

                if(!$productsInserted){
                    return false;
                }else{
                    $invoiceModule = Module::find(getModuleIdFromEntity($this->invoiceModel_));
                    $client = $this->clientEntity_->find($estimate->client_id);
                    $saleTransactionType =  SaleTransactionTypeEnum::CashSale;
                    $this->accountingService_->record(
                                                    $companyId,
                                                    $saleTransactionType,
                                                    $invoiceModule,
                                                    $invoice,
                                                    $this->getInvoiceTransactionId($companyId, $outletId, $invoice->invoice_number),
                                                    $invoice->sub_total
                                                );
                    if($client && $saleTransactionType == SaleTransactionTypeEnum::CreditSale) {
                        $this->clientsService_->addClientReceivables($client->id, $invoice->total);
                    }

                    if($invoice->client_id > 0) {
                        $this->createInvoiceCommentForClient($user, $invoice);
                    }
                    $job = (new GenerateInvoicePDFJob($invoice->id))->delay(now()->addSeconds(1));
                    dispatch($job);
                    return true;
                }

            }else{
                return false;
            }
        });
    }

    public function updateInvoice($request){

        // saving invoice personal data
        $InvoicePersonalData = [

           'payment_type' => $request->payment_type,
           'invoice_date' => $request->invoice_date,
           'due_date' => $request->due_date,
           'sales_person' => $request->sales_person,
           'subject' => $request->subject,
           'customer_note' => $request->customer_note,
           'terms_n_conditions' => $request->terms_n_conditions,
           'status' => $request->status,
           'sub_total' => $request->sub_total,
           'discount' => $request->discount,
           'shipping_charged' => $request->shipping_charged,
           'balance' => $request->balance,
           'amount_paid' => $request->amount_paid,
           'client_id' => $request->client_id,
           'company_id' =>  auth()->user()->company_id,

        ];
        $result = Invoice::where('id', $request->invoice_id)->update($InvoicePersonalData);
        if($result){
            $products = $request->products;
            foreach ($products as $key => $invoiceProduct) {
               InvoiceProduct::where('id', $invoiceProduct['id'])->update($invoiceProduct);
           }
           return true;
        }else{
            return false;
        }
    }

    public function sendInvoice(int $invoiceId){
        $invoice = $this->invoiceModel_->find($invoiceId);
        if($invoice && $invoice->client){
                event(new InvoiceEmailEvent($invoice->client, $invoice, $invoice->products));
                return true;
        }else{
            return false;
        }
    }

    public function createPDF(int $invoiceId){
        $invoice = Invoice::find($invoiceId);
        if($invoice){
            $products = $invoice->products;
            $client = $invoice->client;
            $pdf = PDF::loadView('pdf-files.invoice-pdf', compact('invoice', 'client', 'products'));
            $invoicePDFFilename = "{$invoice->invoice_number}_{$invoice->company_id}";
            $path = $this->mediaService_->getPath(null, true);
            $filename = $invoicePDFFilename.'.pdf';
            $pdf->save($path.$filename);
            return $this->mediaService_->createMediaEntity($path, $filename, $invoice->id, MediaTypesEnum::InvoicesPDF, null, ModuleEnum::Invoices);
        }else{
            return false;
        }
    }

    private function createInvoiceCommentForClient(User $user, Invoice $invoice) {
        $comment = "Invoice " .$invoice->invoice_number ." of ". $invoice->client->currency. $invoice->total. " generated by <strong>".$user->first_name. " ". $user->last_name."</strong>";
        $commentId = Str::uuid();
        $this->commentService_->createComment($comment, $user->id, false, ModuleEnum::Clients, $invoice->client->id, $commentId);
    }

    public function getInvoiceById ($invoiceId) {
        return $this->invoiceModel_->where('id', $invoiceId)->first();
    }

    public function getInvoiceFiles(int $invoiceId) {
        return $this->mediaService_->fetchMediaByEntity(ModuleEnum::Invoices, $invoiceId, [], 'id', 'DESC', 1000);
    }

}

?>
