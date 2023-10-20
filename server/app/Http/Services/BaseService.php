<?php
namespace App\Http\Services;

use App\Http\Services\VendorProductsService;

class BaseService {

    private $productService_;

    public function __construct(VendorProductsService $productService) {
        $this->productService_ = $productService;
    }
    
    public function calculateProductPrice($request){
        $subTotal = 0;
        $tax = 0;
        $data = $request->all();
        $this->productService_->checkAvailableQuantity($data['product_id'], $data['quantity']);
        $product = $this->productService_->getProductData($data['product_id']);
        $total = $this->productService_->getTotal($data['quantity'], $data['price']);
        $discount = 0;
        // discount calculation
        if(!empty($data['discount'])){
            $discount = calculateDiscountAmount($total, $data['discount']);
            $subTotal = $total - $discount;
        }else{
            $subTotal = $total;
        }
        // tax calculation
        if(!is_null($product->vat_code_id)){
            $tax = calculateVatAmount($subTotal, $product->vat->name, $product->vat->param_2 == 'included');
            $subTotal = ( $product->vat->param_2 != 'included') ? ($subTotal - $tax) : ($subTotal + $tax);
        } 
        return response()->json([
            'calculation' =>[
                'quantity' => $data['quantity'],
                'price' => $data['price'],
                'discount' => $discount,
                'tax' => $tax,
                'tax_type' => is_null($product->vat_code_id) ? 'included' : $product->vat->param_2,
                'total' => $total,
                'sub_total' => $subTotal,
            ]       
        ]);
    }

}

?>