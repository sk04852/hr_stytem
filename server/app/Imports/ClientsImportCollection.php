<?php

namespace App\Imports;

use App\Http\Controllers\Clients\Models\Client;
use App\Http\Controllers\Clients\Models\ClientAddress;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;

class ClientsImportCollection implements ToCollection
{
    private $model_;
    private $addresses = [];
    public function __construct(Client $model) {
        $this->model_ = $model;
    }

    public function collection(Collection $collection)
    {
        //
    }

    public function getModel() {
        return $this->model_;
    }

    public function preProcess($data)
    {
        $x = 0;
        $clientAddresses = [];
        foreach($data as $key=> $row) {
                $uuid = (string) Str::uuid();
                $data[$key]['company_id'] = getCompanyId();
                $data[$key]['outlet_id'] = getOutletId();
                $data[$key]['uuid'] = $uuid;
                $data[$key]['username'] = Str::random(8);

                $clientAddresses = [];

                if(isset($data[$key]['address_1'])) {
                    $clientAddresses['address_1'] = $data[$key]['address_1'];
                    unset($data[$key]['address_1']);
                }

                if(isset($data[$key]['address_2'])) {
                    $clientAddresses['address_2'] = $data[$key]['address_2'];
                    unset($data[$key]['address_2']);
                }

                if(isset($data[$key]['zip'])) {
                    $clientAddresses['zip_code'] = $data[$key]['zip'];
                }

                if(isset($data[$key]['city'])) {
                    $clientAddresses['city'] = $data[$key]['city'];
                    unset($data[$key]['city']);
                }

                if(isset($data[$key]['country'])) {
                    $clientAddresses['country'] = $data[$key]['country'];
                    unset($data[$key]['country']);
                }

                if(isset($data[$key]['state'])) {
                    $clientAddresses['state'] = $data[$key]['state'];
                    unset($data[$key]['state']);
                }

                if(isset($data[$key]['attention'])) {
                    $clientAddresses['attention'] = $data[$key]['attention'];
                    unset($data[$key]['attention']);
                }

                $this->addresses[$uuid] = $clientAddresses;
        }
        return $data;
    }

    public function postProcess($data)
    {
        return $data;
    }

    public function process($header, array $data) {

        $builtData = $this->buildData($header, $data);
        $builtData = $this->preProcess($builtData);
        $onlyFillableData = $this->getOnlyFillableData($builtData);
        $this->getModel()->insert($onlyFillableData);
        $clients = $this->getModel()->pluck('id', 'uuid')->toArray();

        $addressesToBeInserted= [];
        foreach($this->addresses as $key=> $address) {
            $address['client_id'] = $clients[$key];
            $addressesToBeInserted[] = $address;
        }

        if(count($addressesToBeInserted) >0) {
            $this->getModel()->address()->insert($addressesToBeInserted);
        }

    }

    public function getOnlyFillableData($data) {
        $fillableData = [];
        foreach($data as $key=> $row) {
            $fillableData[] = $this->getModel()->getOnlyFillables($row);
        }
        return $fillableData;
    }

    public function buildData($header, $data) {
        $builtData = [];
        foreach($data[0] as $key=> $row) {
            $builtRow = [];
            foreach($header as $columnName=> $columnKey) {
                $builtRow[$columnName] = (isset($row[$columnKey]))? $row[$columnKey]: null;
            }
            $builtData[] = $builtRow;
        }
        return $builtData;
    }

    public function getFillable() {
        $fillables =  $this->getModel()->getFillable();
        $addressModel = new ClientAddress();
        $allColumns = [];
        $hiddenColumns = $this->hiddenColumns();
        foreach(array_merge($fillables, $addressModel->getFillable()) as $key=>$value) {
            if(!in_array($value, $hiddenColumns)) {
                $allColumns[] = $value;
            }
        }
        return $allColumns;
    }

    public function hiddenColumns() {
        return [
                "id"
        ];
    }
}
