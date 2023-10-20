<?php

namespace App\Http\Controllers\Brands;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Brands\Models\Brand as ThisModel;
use App\Http\Controllers\Brands\Requests\CreateBrandRequest as CreateRequest;
use App\Http\Controllers\Brands\Requests\UpdateBrandRequest as UpdateRequest;
use App\Http\Controllers\Brands\Requests\DeleteBrandRequest as DeleteRequest;
use App\Http\Controllers\Brands\Requests\DeleteMassBrandRequest as MassDeleteRequest;
use App\Http\Controllers\Brands\Requests\UpdateMassBrandRequest as MassUpdateRequest;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;

class BrandsController extends Controller
{
    const MODULE_NAME = 'brand';
    const COLLECTION_NAME = 'brands';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function brandsList()
    {
        try {

            $filters = [];
            if (Request::has('name')) {
                $filters['name'] = Request::get('name');
            }
            if (Request::has('primary_email')) {
                $filters['primary_email'] = Request::get('primary_email');
            }
            if (Request::has('website')) {
                $filters['website'] = Request::get('website');
            }
            if (Request::has('primary_phone')) {
                $filters['primary_phone'] = Request::get('primary_phone');
            }
            if (Request::has('employees')) {
                $filters['employees'] = Request::get('employees');
            }
            if (Request::has('industry_id')) {
                $filters['industry_id'] = Request::get('industry_id');
            }
            if (Request::has('type')) {
                $filters['type'] = Request::get('type');
            }
            if (Request::has('assigned_to')) {
                $filters['assigned_to'] = Request::get('assigned_to');
            }
            if (Request::has('billing_country')) {
                $filters['billing_country'] = Request::get('billing_country');
            }
            if (Request::has('billing_city')) {
                $filters['billing_city'] = Request::get('billing_city');
            }
            if (Request::has('billing_state')) {
                $filters['billing_state'] = Request::get('billing_state');
            }
            if (Request::has('shipping_country')) {
                $filters['shipping_country'] = Request::get('shipping_country');
            }
            if (Request::has('shipping_city')) {
                $filters['shipping_city'] = Request::get('shipping_city');
            }
            if (Request::has('shipping_state')) {
                $filters['shipping_state'] = Request::get('shipping_state');
            }
            if (Request::has('created_by')) {
                $filters['created_by'] = Request::get('created_by');
            }
            $records = $this->model->filter($filters)->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            return $this->created([BrandsController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function createBrand(CreateRequest $request)
    {
        try {
            $this->model->fill($request->all());
            $this->model->created_by = $this->userId();
            if ($this->model->save()) {
                $this->addLog(LogTypeEnum::Info, null, $this->model, $this->model, LogAction::Created, ModuleEnum::Brands);
                return $this->created([BrandsController::MODULE_NAME => $this->model, 'message' => BrandsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function brandDetail($id)
    {
        try {
            $record = $this->model->find($id);
            if (!empty($record)) {
                return $this->created([BrandsController::MODULE_NAME => $record]);
            }
            return $this->noRecord(["message" => BrandsController::RECORD_NOT_FOUND], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function updateBrand(UpdateRequest $request)
    {
        try {
            $id = $request->id;
            $record = $this->model->find($id);
            $record->fill($request->all());
            $oldData = $record->getOriginal();
            if ($record->save()) {
                $this->addLog(LogTypeEnum::Info, null, $oldData, $record, LogAction::Updated, ModuleEnum::Brands);
                return $this->created([BrandsController::MODULE_NAME => $record, 'message' => BrandsController::RECORD_UPDATED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function deleteBrand(DeleteRequest $request)
    {
        try {
            $id = $request->id;
            $record = $this->model->find($id);
            if ($record->delete()) {
                $this->addLog(LogTypeEnum::Info, null, $record, null, LogAction::Deleted, ModuleEnum::Brands);
                return $this->created(['message' => BrandsController::RECORD_DELETED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massDelete(MassDeleteRequest $request)
    {
        try {
            $data = $request->all();
            $ids = $data['id'];
            $this->model->whereIn('id', $ids)->delete();
            return $this->created(['message' => BrandsController::MASS_RECORDS_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function massUpdate(MassUpdateRequest $request)
    {
        try {
            $allData = $request->all();
            $brandsIds = $allData['id'];
            $updateData = $request->except(['id']);
            foreach ($brandsIds as $key => $value) {
                $record = $this->findOneById($brandsIds[$key]);
                if ($updateData['name'][$key]) {
                    $record->name = $updateData['name'][$key];
                }
                if ($updateData['primary_email'][$key]) {
                    $record->primary_email = $updateData['primary_email'][$key];
                }
                if ($updateData['assigned_to'][$key]) {
                    $record->assigned_to = $updateData['assigned_to'][$key];
                }
                $record->save();
            }
            return $this->created([$this->model->whereIn('id', $brandsIds)->get(), 'message' => BrandsController::MASS_RECORDS_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function search($name)
    {
        try {
            $record = $this->model->where('name', 'LIKE', "%{$name}%")
                ->orWhere('primary_email', 'LIKE', "%{$name}%")
                ->orWhere('primary_phone', 'LIKE', "%{$name}%")
                ->orWhere('billing_address', 'LIKE', "%{$name}%")
                ->orWhere('shipping_address', 'LIKE', "%{$name}%")
                ->get();
            if ($record->isNotEmpty()) {
                return $this->created([BrandsController::COLLECTION_NAME => $record]);
            }
            return $this->noRecord([BrandsController::COLLECTION_NAME => $record, "message" => BrandsController::RECORD_NOT_FOUND], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
