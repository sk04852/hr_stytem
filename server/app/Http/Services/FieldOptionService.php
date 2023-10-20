<?php
namespace App\Http\Services;

use App\Http\Controllers\Options\Models\FieldOption;
use App\Http\Controllers\Options\Models\FieldOptionType;
use App\Http\Controllers\Options\Models\FieldOptionTypeCompanyMap;

class FieldOptionService {

    private $model_;
    private $fieldOptionTypeModel_;
    private $fieldOptionTypeCompanyMapModel_;
    public function __construct(
                                FieldOption $model,
                                FieldOptionType $fieldOptionTypeModel,
                                FieldOptionTypeCompanyMap $fieldOptionTypeCompanyMapModel)
    {
        $this->model_ = $model;
        $this->fieldOptionTypeModel_ = $fieldOptionTypeModel;
        $this->fieldOptionTypeCompanyMapModel_ = $fieldOptionTypeCompanyMapModel;
    }

    public function createFieldOptionType(string $description, string $comment) {
        return $this->fieldOptionTypeModel_->create(['type_description'=> $description, 'comment'=> $comment]);
    }

    public function createFieldOption(int $typeId, string $name, string $description, int $sortOrder = 0) {
        return $this->model_->create(['type_id'=> $typeId, 'name'=> $name, 'description'=> $description, 'sort_order'=> $sortOrder]);
    }

    public function associateFieldOptionToCompany(int $fieldOptionTypeId, int $companyId) {
        $this->fieldOptionTypeCompanyMapModel_->create(['field_option_type_id'=> $fieldOptionTypeId, 'company_id'=> $companyId]);
    }

    public function dissociateFieldOptionFromCompany(int $fieldOptionTypeId, int $companyId) {
        $this->fieldOptionTypeCompanyMapModel_->where(['field_option_type_id'=> $fieldOptionTypeId, 'company_id'=> $companyId])->delete();
    }

    public function getCompanyFieldOptions(int $companyId) {
        return $this->fieldOptionTypeCompanyMapModel_->where('company_id', $companyId)->with('fieldOptionTypes.fieldOptions')->get();
    }

    public function getFieldOptionsTypeById(int $id) {
        return $this->fieldOptionTypeModel_->where('id', $id)->first();
    }

}

?>
