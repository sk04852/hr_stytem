<?php
namespace App\Http\Services;

use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Watchers\Models\Watcher;


class WatchersService {
    private $model_;
    
    public function __construct(Watcher $model, UserService $userSevice) {
        $this->model_ = $model;
    }

    public function create(int $moduleId, int $relationId, int $userId) {
        $alreadyWatching = $this->model_->where(['module_id'=> $moduleId, 'relation_id'=> $relationId, 'user_id'=> $userId])->first(); 
        if($alreadyWatching)
            return;
        $watcher = $this->model_->fill(['module_id'=> $moduleId, 'relation_id'=> $relationId, 'user_id'=> $userId]);
        $watcher->save();
        return $watcher;
    }
    
    public function remove(int $moduleId, int $relationId, int $userId) {
        $this->model_->where(['module_id'=> $moduleId, 'relation_id'=> $relationId, 'user_id'=> $userId])->delete();
    }
    
    public function getWatchers(int $moduleId, int $relationId) {
        return $this->model_->where(['module_id'=> $moduleId, 'relation_id'=> $relationId])->get();
    }

}

?>