<?php

namespace App\Http\Controllers\Tags;

use App\Http\Controllers\Tags\Requests\CreateTagsRequest;
use App\Http\Controllers\Tags\Requests\DeleteTagsRequest;
use App\Http\Controllers\Tags\Requests\UpdateTagsRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Tags\Models\Tag;
use Illuminate\Http\Request;
use Exception;

class TagsController extends Controller
{
    const MODULE_NAME = 'Tags';
    const TAG_CREATED = 'New Tag created successfully';
    const TAG_NOT_CREATED = 'Error in creating a Languages';
    const COLLECTION_NAME = 'Tags';
    const TAG_UPDATED = 'Tag updated successfully';
    const TAG_NOT_UPDATED = 'Error in updating Tag';
    const TAG_DELETED = 'Tag deleted successfully';
    const TAG_NOT_DELETED = 'Error in deleting Tag';
    const TAG_ALREADY_MARKED = 'Tag already marked';
    const TAG_NOT_FOUND = 'Tag not found';

    public function __construct(Tag $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $Tags = $this->model;
            if ($Tags) {
                $Tags = $this->model
                    ->orderBy($this->getSortBy(), $this->getSort())
                    ->paginate($this->getPerPage());
                if ($Tags->isNotEmpty()) {
                    return $this->created([TagsController::COLLECTION_NAME => $Tags]);
                }
            }
            return $this->noRecord(['message' => TagsController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateTagsRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => TagsController::TAG_CREATED]);
            }
            return $this->failed(['message' => TagsController::TAG_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateTagsRequest $request)
    {
        try {
            $updatedTags = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedTags) {
                return $this->created(['message' => TagsController::TAG_UPDATED]);
            }
            return $this->failed(['message' => TagsController::TAG_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteTagsRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => TagsController::TAG_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

