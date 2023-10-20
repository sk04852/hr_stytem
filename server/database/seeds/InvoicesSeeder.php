<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Invoices\Models\Invoice;
use Faker\Factory as Faker;


class InvoicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('App\Http\Controllers\Generics\Models\Customer');
        for($i=0 ; $i<=10; $i++) {
        
            $invoice = new Invoice();
            $invoice->invoice_number = $faker->randomNumber($nbDigits = 8, $strict = false);
            $invoice->product_id = 1;
            $invoice->price = $faker->randomDigit();
            $invoice->quantity = $faker->randomDigit();
            $invoice->total = $faker->randomDigit();
            $invoice->payable_amount = $faker->randomDigit();
            $invoice->client_id = 1;
            $invoice->save();
        }
    }
}
