<?php

namespace App\Http\Controllers\Clients\Models;

use App\Http\Controllers\Temp\Product;
use App\Models\BaseModel;

class ClientProductPrice extends BaseModel
{
    protected $table = 'client_product_prices';
    protected $primaryKey = 'id';
    protected $fillable = [
        'client_id',
        'product_id',
        'price'
    ];

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

}
