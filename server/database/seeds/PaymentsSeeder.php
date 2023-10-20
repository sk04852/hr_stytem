<?php

use App\Http\Controllers\Payments\Models\Payment;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PaymentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentFaker = Faker::create(Payment::class);
        for ($i = 0; $i <= 20; $i++) {
            $data = [
                'member_id' => $paymentFaker->unique()->numberBetween(1, 21),
                'payment_method' => $paymentFaker->randomElement($array = ['Stripe', 'Paypal', 'Credit Card', 'Other']),
                'payment_status' => 1,
                'paid_amount' => $paymentFaker->randomElement($array = [50000, 20000, 30000, 25000]),
                'payment_gateway_response' => $paymentFaker->sentence(),
            ];
            $payment = new Payment($data);
            $payment->save();
        }
    }
}
