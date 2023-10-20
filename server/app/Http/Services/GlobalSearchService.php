<?php
namespace App\Http\Services;

use App\Http\Controllers\Accounts\Models\Account;
use App\Http\Controllers\Leads\Models\Lead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GlobalSearchService {

    private $wildCardSearch = true;

    private $searchableModels = [
        Account::class,
        Lead::class
    ];

    public function __construct() {

    }

    public function search(string $keywords) {
        $searchResults = [];
        if($keywords != null && !empty($keywords)) {
            foreach($this->searchableModels as $model) {
                $modelName = Str::plural(strtolower(Arr::last(explode("\\", $model))));
                $searchResults[$modelName] =
                app($model)->search($this->getSearchQuery($keywords))->rule(function($builder) {
                    return [
                        'must' => [
                            'query_string' => [
                                'query' => $builder->query
                            ]
                        ]
                    ];
                })->get()->map(function($model){
                    return $this->resolveModel($model);
                });
            }
        }
        return ['results'=> $searchResults];
    }

    public function resolveModel(Model $model) {
        if($model instanceof Account) {
            return [
                    'id'=> $model->id,
                    'text'=> $model->user->first_name . " " . $model->user->last_name,
                    'description'=> $model->account_name,
                    'link'=> 'account/'.$model->id,
                    'module'=> 'Accounts'
                   ];
        } else if($model instanceof Lead) {
            return [
                'id'=> $model->id,
                'text'=> $model->first_name . " " . $model->last_name,
                'description'=> 'Lead #'.$model->id,
                'link'=> 'lead/'.$model->id,
                'module'=> 'Leads'
            ];
        }
    }

    private function getSearchQuery($keywords) {
        $searchQuery = "";
        if($this->wildCardSearch) {
            $searchQuery = "*".$keywords."*";
        } else {
            $searchQuery = $keywords;
        }

        return $searchQuery;
    }
}

?>
