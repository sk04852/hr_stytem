<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Exception;
use App\Http\Controllers\Clients\Models\ClientAddress;
use App\Http\Controllers\Clients\Requests\UpdateClientAddressesRequest as UpdateRequest;
use App\Http\Services\ClientsService;
use Illuminate\Http\Request;

class ClientsAddressesController extends Controller
{
    const MODULE_NAME = 'client_addresses';
    const COLLECTION_NAME = 'addresses';


    public function __construct(ClientAddress $model, ClientsService $clientService)
    {
        parent::__construct($model);
        $this->clientService_ = $clientService;

    }

    public function index(Request $request)
    {
        try {
            $clientAddresses = $this->model->clientAddressesFilter($request)->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if ($clientAddresses->isNotEmpty()) {
                return $this->created([ClientsAddressesController::COLLECTION_NAME => $clientAddresses]);
            }
                return $this->noRecord(['message'=> ClientsAddressesController::RECORD_NOT_FOUND, ClientsAddressesController::COLLECTION_NAME => $clientAddresses],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $data = $request->all();
            $isUpdated = $this->model->find($request->id)->update($data);
            if ($isUpdated) {
                return $this->created(['message' => ClientsAddressesController::RECORD_UPDATED]);
            }
                return $this->failed(['message' => ClientsAddressesController::RECORD_NOT_UPDATED]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(int $id)
    {
        try {
            $client = $this->model->find($id);
            if ($client) {
                $client->delete();
                return $this->created(['message' => ClientsAddressesController::RECORD_DELETED]);
            }
                return $this->noRecord(['message' => ClientsAddressesController::RECORD_NOT_FOUND],200);

        }catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        }catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

}
