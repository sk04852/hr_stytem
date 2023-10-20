<?php
namespace App\Http\Services;

use Exception;
use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Clients\Models\ClientProductPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class ClientsService {

    private $clientModel_;
    private $clientProductPriceModel_;
    public function __construct(
                                Client $client,
                                ClientProductPrice $clientProductPriceModel)
    {
        $this->clientModel_ = $client;
        $this->clientProductPriceModel_ = $clientProductPriceModel;
    }

    public function fetchClients(int $companyId, int $outletId) {
        $allclients = $this->clientModel_->where('company_id', $companyId)
        ->select(DB::raw('id, CONCAT(first_name, " ",last_name) as name'))->get();
        if($allclients->isNotEmpty()){
            return response()->json([
                'clients' => $allclients
            ]);
        }
        return response(['message' => 'Clients not found'], 200);
    }

    public function getClientInformation($clientId) {
        return $this->clientModel_->select('id','first_name','last_name','email')->where('id',$clientId)->first();
    }

    public function getClient(int $clientid, $with = []) {
        return $this->clientModel_->where('id', $clientid)->with($with)->first();
    }

    public function addClientReceivables(int $clientId, float $amount) {
        $client = $this->getClient($clientId);
        $client->outstanding_receivables = $client->outstanding_receivables + $amount;
        $client->save();
    }

    public function addClientCredit(int $clientId, float $amount) {
        $client = $this->getClient($clientId);
        $client->outstanding_receivables = $client->outstanding_receivables + $amount;
        $client->save();
    }

    public function addProductPrices(int $clientId, array $clientProductPrices) {
        $client = $this->getClient($clientId);
        if(!$client) {
            throw new Exception("Client not found, unable to allocate product prices");
        }
        $existingProductDictionary = $client->productPrices->mapToDictionary(function ($item, $__) {
            return [$item['product_id'] => $item['uuid']];
        })->toArray();

        $existingProductIds = array_keys($existingProductDictionary);
        $pricesTobeInserted = []; $toBeUpdated = [];
        foreach($clientProductPrices as $key=> $productPrice) {
            if(in_array($productPrice['product_id'], $existingProductIds)) {
                $toBeUpdated[$existingProductDictionary[$productPrice['product_id']][0]] = $productPrice['price'];
            } else {
                $pricesTobeInserted[] = ['product_id'=> $productPrice['product_id'], 'price'=> $productPrice['price'],'client_id'=> $clientId, 'uuid'=> Str::uuid(), 'created_at'=> now()];
            }
        }

        if(count($pricesTobeInserted)) {
            $client->productPrices()->insert($pricesTobeInserted);
        }

        if(count($toBeUpdated)) {
            $this->clientProductPriceModel_->massUpdate($toBeUpdated, 'price', 'uuid', true);
        }
    }
}

?>
