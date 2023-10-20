<?php
namespace App\Http\Services;

use App\Http\Controllers\Comments\Models\Comment;

class CommentService {
    private $model_;

    public function __construct(Comment $model) {
        $this->model_ = $model;
    }

    public function createComment(string $comment, int $userId, bool $isTemplate, int $moduleId, int $relationId, string $uuid) {
        $this->model_->module_id = $moduleId;
        $this->model_->relation_id = $relationId;
        $this->model_->comment = $comment;
        $this->model_->created_by = $userId;
        $this->model_->is_template = $isTemplate;
        $this->model_->uuid = $uuid;
        $this->model_->is_reply = "No";

        $this->model_->save();
        return $this->model_->id;
    }
}

?>
