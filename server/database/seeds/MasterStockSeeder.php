<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\MasterStocks\Models\MasterStock;

class MasterStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=1 ; $i<=5; $i++) {
        
            $masterStock = new MasterStock();
            $masterStock->available_quantity = 100;
            $masterStock->product_id = $i;
            $masterStock->save();
        }
    }
}
