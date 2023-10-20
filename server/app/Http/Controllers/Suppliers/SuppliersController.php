<?php

namespace App\Http\Controllers\Suppliers;

use Exception;
use App\Models\Module;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Suppliers\Models\Supplier as ThisModel;
use App\Http\Controllers\Suppliers\Requests\CreateSupplierRequest as CreateRequest;
use App\Http\Controllers\Suppliers\Requests\UpdateSupplierRequest as UpdateRequest;
use Illuminate\Http\Request;


class SuppliersController extends Controller
{
    const MODULE_NAME = 'supplier';
    const ITEM = 'supplier';
    const COLLECTION_NAME = 'records';
    const SUPPLIER_CREATED = 'New supplier has been created successfully';
    const SUPPLIER_CREATE_FAILED = 'Failed to create new supplier';
    const SUPPLIER_NOT_FOUND = 'Supplier not found';
    const SUPPLIER_UPDATE_FAILED ='Failed to update supplier';
    const SUPPLIER_DELETE_FAILED ='Failed to delete supplier';


    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $suppliers = $this->model->suppliersFilter($request)->forThisCompany($this->companyId())->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            if ($suppliers->isNotEmpty()) {
               return $this->created([SuppliersController::COLLECTION_NAME => $suppliers]);
            }
            return $this->noRecord(['message' => SuppliersController::NO_RECORD, SuppliersController::COLLECTION_NAME => $suppliers],200);

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function store(CreateRequest $request)
    {
        try {
            $data =  $request->all();
            $data['company_id'] = $this->companyId();
            $data['created_by'] = $this->userId();
            $result = $this->model->create($data);
            if ($result) {
                return $this->created(['message' => SuppliersController::SUPPLIER_CREATED, SuppliersController::ITEM=> $result]);
            }
                return $this->failed(['message' => SuppliersController::SUPPLIER_CREATE_FAILED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show(int $supplierId)
    {
        try {
            $service = $this->model->find($supplierId);
            if (!$service) {
                return $this->noRecord(['message' => SuppliersController::SUPPLIER_NOT_FOUND],200);
            }
                return $this->created([SuppliersController::MODULE_NAME => $service]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $updateSupplier = $this->model->find($request->id)->update($request->all());
            if ($updateSupplier) {
                return $this->created(['message' => SuppliersController::RECORD_UPDATED]);
            }
                return $this->failed(['message' => SuppliersController::SUPPLIER_UPDATE_FAILED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(int $supplierId)
    {
        try {
            $supplier = $this->model->find($supplierId);
            if ($supplier) {
                if ($supplier->delete()) {
                    return $this->created(['message'=> SuppliersController::RECORD_DELETED]);
                }
                return $this->failed(['message'=> SuppliersController::SUPPLIER_DELETE_FAILED]);
            }
                return $this->noRecord(['message' => SuppliersController::SUPPLIER_NOT_FOUND],200);

        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

}
