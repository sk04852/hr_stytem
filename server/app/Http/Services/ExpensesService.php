<?php
namespace App\Http\Services;


use App\Http\Controllers\Expenses\Models\ExpenseCategoryUser;
use App\Http\Controllers\People\PeopleController;


class ExpensesService extends PeopleController {

    private $model_;
    private $userService_;

    public function __construct(
                                ExpenseCategoryUser $model,
                                UserService $userService
                                ) {
        $this->model_ = $model;
        $this->userService_ = $userService;
    }

    public function shareExpenseCategoryWithUsers(array $categoryUsers) {
        $this->model_->insert($categoryUsers);
    }

    public function shareExpenseCategoryWithUser(int $categoryId, int $userId) {
        $this->model_ = $this->model_->newInstance();
        $this->model_->category_id = $categoryId;
        $this->model_->user_id = $userId;
        return $this->model_->save();
    }

    public function deleteExpenseCategoryWithUser(int $categoryId, int $userId) {
        $record = $this->model_->where('category_id', $categoryId)->where('user_id',$userId)->first();
        return $record ? $record->delete() :false; 
    }
}

?>