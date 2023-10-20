<?php
namespace App\Http\Services;

use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Clients\Models\ClientAddress;
use App\Http\Controllers\Invoices\Models\Invoice;
use App\Http\Controllers\Payments\Models\Payment;
use Illuminate\Support\Facades\DB;

class ClientSummeryService{

    private $clientModel_;

    public function __construct(Client $client)
    {
        $this->clientModel_ = $client;
    }

    public function invoicesQuery($clientId){
        return DB::table('invoices')
                        ->select(DB::raw('COUNT(client_id) as total_invoices, SUM(total) as total'), 'created_at', 'updated_at')
                        ->groupBy('client_id')->where('client_id', $clientId)->get();
    }

    public function paymentsQuery($clientId){
        return DB::table('payments')
                        ->select(DB::raw('COUNT(client_id) as total_payments, SUM(amount) as total_amount'), 'created_at', 'updated_at')
                        ->groupBy('client_id')->where('client_id', $clientId)->get();
    }

    public function addressesQuery($clientId){
        return DB::table('client_addresses')
                        ->select('*')->where('client_id', $clientId)->get();
    }

    public function generateSummery($client){
        return response()->json([
            'summery' => [
                'client' => [
                    'client_id' => $client->id,
                    'company_name' => $client->company_name,
                    'first_name' => $client->first_name,
                    'last_name' =>  $client->last_name,
                    'email' => $client->email,
                    'phone' => is_null($client->phone) ? '0' : $client->phone,
                    'mobile' => is_null($client->mobile) ? '0' : $client->mobile,
                    'outstanding_receivables' => $client->outstanding_receivables,
                    'unused_credits' => $client->unused_credits,
                    'created_at' => $client->created_at,
                    'updated_at' => $client->updated_at,
                ],
                'client_addresses' => [
                    'total_addresses' => count($this->addressesQuery($client->id)),
                    'list' => $this->addressesQuery($client->id),
                ],
                'invoices' => $this->invoicesQuery($client->id),
                'payments' => $this->paymentsQuery($client->id),
                'statements' => 200,
                'quotations' => 100,
            ]
        ]);
    }
    
}

?>