<?php

namespace App\Http\Controllers\Languages;

use App\Http\Controllers\Languages\Requests\CreateLanguagesRequest;
use App\Http\Controllers\Languages\Requests\DeleteLanguagesRequest;
use App\Http\Controllers\Languages\Requests\UpdateLanguagesRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Languages\Models\Language;
use Illuminate\Http\Request;
use Exception;

class LanguagesController extends Controller
{
    const MODULE_NAME = 'Languages';
    const LANGUAGE_CREATED = 'New Languages created successfully';
    const LANGUAGE_NOT_CREATED = 'Error in creating a Languages';
    const COLLECTION_NAME = 'Languages';
    const LANGUAGE_UPDATED = 'Languages updated successfully';
    const LANGUAGE_NOT_UPDATED = 'Error in updating Languages';
    const LANGUAGE_DELETED = 'Languages deleted successfully';
    const LANGUAGE_NOT_DELETED = 'Error in deleting Languages';
    const LANGUAGE_ALREADY_MARKED = 'Languages already marked';
    const LANGUAGE_NOT_FOUND = 'Languages not found';

    public function __construct(Language $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {

        try {
            $Languages = $this->model;
            if ($Languages) {
                $Languages = $this->model
                    ->orderBy($this->getSortBy(), $this->getSort())
                    ->paginate($this->getPerPage());
                if ($Languages->isNotEmpty()) {
                    return $this->created([LanguagesController::COLLECTION_NAME => $Languages]);
                }
            }
            return $this->noRecord(['message' => LanguagesController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateLanguagesRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => LanguagesController::LANGUAGE_CREATED]);
            }
            return $this->failed(['message' => LanguagesController::LANGUAGE_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateLanguagesRequest $request)
    {
        try {
            $updatedLanguages = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedLanguages) {
                return $this->created(['message' => LanguagesController::LANGUAGE_UPDATED]);
            }
            return $this->failed(['message' => LanguagesController::LANGUAGE_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteLanguagesRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => LanguagesController::LANGUAGE_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

