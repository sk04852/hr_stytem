<?php

namespace App\Http\Controllers\Options;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Options\Requests\CreateOptionRequest as CreateRequest;
use App\Http\Controllers\Options\Requests\UpdateOptionRequest as UpdateRequest;
use App\Http\Controllers\Options\Requests\CreateFieldOptionTypeRequest;
use App\Http\Controllers\Options\Models\FieldOptionType;
use App\Http\Controllers\Options\Models\FieldOption as ThisModel;
use App\Http\Services\FieldOptionService;

class FieldOptionsController extends Controller
{
    const MODULE_NAME = 'field_option';
    const COLLECTION_NAME = 'field_options';
    const COMPANY_MAP = 'field_option_types_company_map';
    private $fieldOptionService_;

    public function __construct(
                                ThisModel $model,
                                FieldOptionService $fieldOptionService)
    {
        parent::__construct($model);
        $this->fieldOptionService_ = $fieldOptionService;
    }

    public function index()
    {
        try {
            $records = $this->model->toDigest();
            $this->fieldOptionService_->dissociateFieldOptionFromCompany(1, 1);
            return $this->created([FieldOptionsController::COLLECTION_NAME => $records]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function create()
    {
        return;
    }

    public function store(CreateRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if($this->model->save()) {
                return $this->created([FieldOptionsController::MODULE_NAME => $this->model, 'message'=> FieldOptionsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $record = $this->findOneById($id);
            return $this->created([FieldOptionsController::MODULE_NAME => $record]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function edit($id)
    {
        return;
    }

    public function update(UpdateRequest $request, $id)
    {
        try {
            $record = $this->findOneById($id);
            if($record) {
                $fillables = $this->model->getOnlyFillables($request->all());
                $record->fill($request->all());
                if($record->save()) {
                    return $this->created([FieldOptionsController::MODULE_NAME => $record, 'message'=> FieldOptionsController::RECORD_UPDATED]);
                }
            } else {
                return $this->created(['message'=> FieldOptionsController::RECORD_NOT_FOUND], 404);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $record = $this->findOneById($id);
            if($record->delete()) {
                return $this->created(['message'=> FieldOptionsController::RECORD_DELETED]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function retrieveFieldOptionsByTypeId(int $typeId) {
        return FieldOptionType::where('id', $typeId)->select(['id','type_description','comment'])->with('fieldOptions')->get();
    }

    public function retrieveFieldOptions() {
        $fieldOptions =  FieldOptionType::select(['id','type_description','comment'])->with('fieldOptions')->get();
        $options = [];
        foreach($fieldOptions as $fieldOption) {
            $options[$fieldOption->id] = $fieldOption;
        }
        return $options;
    }

    public function getFieldOptionsForCompany() {
        try {
            $fieldOptions = $this->fieldOptionService_->getCompanyFieldOptions($this->companyId());
            return $this->created([FieldOptionsController::COMPANY_MAP => $fieldOptions]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function createFieldOptionType(CreateFieldOptionTypeRequest $request) {
        try {
            $comment = ($request->comment == null)? '': $request->comment;
            if($fieldOptionType = $this->fieldOptionService_->createFieldOptionType($request->description, $comment)) {
                $this->fieldOptionService_->associateFieldOptionToCompany($fieldOptionType->id, $this->companyId());
                return $this->created(['field_option_type' => $fieldOptionType, 'message'=> FieldOptionsController::RECORD_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function updateFieldOptionType(CreateFieldOptionTypeRequest $request) {
        try {
            $fieldOptionType = $this->fieldOptionService_->getFieldOptionsTypeById($request->id);
            $fieldOptionTypeUpdatedData = ['type_description'=> $request->description, 'comment'=> $request->comment];
            if($fieldOptionType->update($fieldOptionTypeUpdatedData)) {
                $fieldOptions = $request->fieldOptions;
                $collection = collect($fieldOptions);

                $collection->filter(function($value, $key) {
                    return $value['record_state'] == 1;
                })->each(function($fieldOption) {
                    $onlyFillables = $this->model->getOnlyFillables($fieldOption);
                    $this->model->where('id', $fieldOption['id'])->update($onlyFillables);
                });

                $collection->filter(function($value, $key) {
                    return $value['record_state'] == 0;
                })->each(function($fieldOption) {
                    $onlyFillables = $this->model->getOnlyFillables($fieldOption);
                    $this->model->create($onlyFillables);
                });

                return $this->created(['field_option_type' => $fieldOptionType, 'message'=> FieldOptionsController::RECORD_UPDATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function deleteFieldOptionType(FieldOptionType $fieldOptionType) {
        // This will also delete the relation field options & other associations.
        // Delete functionality is inside boot function of the model
        try {
            $fieldOptionType->delete();
            return $this->created(['message'=> FieldOptionsController::RECORD_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

}
