<?php

namespace App\Http\Controllers\PromoCodes;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Modules\Enums\LogAction;
use App\Http\Controllers\Modules\Enums\LogTypeEnum;
use App\Http\Controllers\Modules\Enums\ModuleEnum;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\PromoCodes\Models\PromoCode as ThisModel;
use App\Http\Controllers\PromoCodes\Requests\CreatePromoCodeRequest as CreateRequest;
use App\Http\Controllers\PromoCodes\Requests\UpdatePromoCodeRequest as UpdateRequest;
use App\Http\Controllers\PromoCodes\Requests\DeletePromoCodeRequest as DeleteRequest;

class PromoCodesController extends Controller
{
    const MODULE_NAME = 'promo_code';
    const COLLECTION_NAME = 'promo_codes';

    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function promoCodesList()
    {
        try {

            $filters = [];
            if (Request::has('promo_code')) {
                $filters['promo_code'] = Request::get('promo_code');
            }
            if (Request::has('company_id')) {
                $filters['company_id'] = Request::get('company_id');
            }
            if (Request::has('brand_id')) {
                $filters['brand_id'] = Request::get('brand_id');
            }
            if (Request::has('user_id')) {
                $filters['user_id'] = Request::get('user_id');
            }
            if (Request::has('status')) {
                $filters['status'] = Request::get('status');
            }

            $records = $this->model->withCompany()->withUser()->withBrand()->filter($filters)->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            return $this->created([PromoCodesController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function createPromoCode(CreateRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                $this->addLog(LogTypeEnum::Info, null, $this->model, $this->model, LogAction::Created, ModuleEnum::PromoCodes);
                return $this->created([PromoCodesController::MODULE_NAME => $this->model, 'message' => PromoCodesController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function promoCodeDetail($id)
    {
        try {
            $record = $this->model->withCompany()->withUser()->withBrand()->find($id);
            if (!empty($record)) {
                return $this->created([PromoCodesController::MODULE_NAME => $record]);
            }
            return $this->noRecord(["message" => PromoCodesController::RECORD_NOT_FOUND], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function updatePromoCode(UpdateRequest $request)
    {
        try {
            $record = $this->model->find($request->id);
            $record->fill($request->all());
            $oldData = $record->getOriginal();
            if ($record->update()) {
                $this->addLog(LogTypeEnum::Info, null, $oldData, $record, LogAction::Updated, ModuleEnum::PromoCodes);
                return $this->created([PromoCodesController::MODULE_NAME => $record, 'message' => PromoCodesController::RECORD_UPDATED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function deletePromoCode(DeleteRequest $request)
    {
        try {
            $record = $this->model->where('id', $request->id)->where('status', 'Inactive')->first();
            if ($record) {
                $record->delete();
                $this->addLog(LogTypeEnum::Info, null, $record, null, LogAction::Deleted, ModuleEnum::PromoCodes);
                return $this->created(['message' => PromoCodesController::RECORD_DELETED]);
            }
            return $this->created(['message' => PromoCodesController::RECORD_NOT_DELETED]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function searchPromoCode($promoCode)
    {
        try {
            $records = $this->model->where('promo_code', 'like', '%' . $promoCode . '%')->get();
            if ($records->isNotEmpty()) {
                return $this->created([PromoCodesController::COLLECTION_NAME => $records]);
            }
            return $this->noRecord(["message" => PromoCodesController::RECORD_NOT_FOUND], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
