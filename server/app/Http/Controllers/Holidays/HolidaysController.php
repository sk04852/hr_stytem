<?php

namespace App\Http\Controllers\Holidays;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Holidays\Models\Holiday as ThisModel;
use App\Http\Controllers\Holidays\Requests\CreateHolidaysRequest;
use App\Http\Controllers\Holidays\Requests\UpdateHolidaysRequest;
use Exception;

class HolidaysController extends Controller
{
    const MODULE_NAME = 'holiday';
    const COLLECTION_NAME = 'holidays';
    const HOLIDAYS_NOT_FOUND = 'Holidays not found';
    const HOLIDAY_NOT_FOUND = 'Holiday not found';
    const HOLIDAY_CREATED = 'Holiday has been created successfully';
    const HOLIDAY_UPDATED = 'Holiday has been updated successfully';
    const HOLIDAY_DELETED = 'Holiday has been deleted successfully';
    const HOLIDAY_CREATE_FAILED = 'Failed to create holidays record';
    const HOLIDAY_UPDATE_FAILED = 'Failed to update holidays record';

    public function __construct(ThisModel $model)
    {
        $this->model = $model;
    }


    public function index()
    {
        try {
            $holidays = $this->model
                    ->orderBy($this->getSortBy(), $this->getSort())
                    ->paginate($this->getPerPage());
            return $this->created([HolidaysController::COLLECTION_NAME => $holidays]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function store(CreateHolidaysRequest $request)
    {
        try {
            $holidayCreated = $this->model->create($request->all());
            if ($holidayCreated) {
                return $this->created(['message' => HolidaysController::HOLIDAY_CREATED]);
            }
            return $this->failed(['message' => HolidaysController::HOLIDAY_CREATE_FAILED]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function update(UpdateHolidaysRequest $request)
    {
        try {
            $updatedHoliday = $this->model->find($request->id)->update($request->all());
            if ($updatedHoliday) {
                return $this->created(['message' => HolidaysController::HOLIDAY_UPDATED]);
            }
            return $this->failed(['message' => HolidaysController::HOLIDAY_UPDATE_FAILED]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function destroy(int $id)
    {
        try {
            $holiday = $this->model->find($id);
            if ($holiday) {
                $holiday->delete();
                return $this->created(['message' => HolidaysController::HOLIDAY_DELETED]);
            }
            return $this->noRecord(['message' => HolidaysController::HOLIDAY_NOT_FOUND]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}
