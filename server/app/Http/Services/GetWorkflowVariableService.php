<?php
namespace App\Http\Services;

class GetWorkflowVariableService {
    public function __construct() {
    }
    public function find($model, $varName) {
        switch ($varName) {
            case "email":
                return $model->email;
            break;
            case "name":
                return $model->first_name." ".$model->last_name;
            break;
            case "first_name":
                return $model->first_name;
            break;
            case "last_name":
                return $model->last_name;
            break;
            case "username":
                return $model->username;
            break;
            default:
                return null;
        }
    }

    public function getVar($model, $variables) {
        $body = [];
        if($variables != null) {
            $variables = explode(',', $variables);
            foreach($variables as $variable) {
                $varName = substr($variable, 1);
                $body[$varName] = $this->find($model,$varName);
            }
        }
        return $body;
    }
}

?>