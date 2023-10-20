<?php
namespace App\Http\Controllers\People\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Controllers\Users\Requests\UserUpdateRequest;
use Illuminate\Support\Str;

class PersonUpdateRequest extends FormRequest
{
    private $userUpdateRequest_;

    public function __construct(
        UserUpdateRequest $userUpdateRequest) {
            $this->userUpdateRequest_ = $userUpdateRequest;
    }

    public function authorize()
    {
        return true;
    }

   /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return $this->getConditionalRules();
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    private function getConditionalRules() {
        $entity =  Str::of($this->url())->after('api/');
        $rules = [];
        switch($entity){
            case "users":
                $rules = $this->userUpdateRequest_->rules();
            break;
        }
        return $rules;
    }
}

?>
