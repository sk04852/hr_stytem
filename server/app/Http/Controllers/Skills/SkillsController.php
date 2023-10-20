<?php

namespace App\Http\Controllers\Skills;

use App\Http\Controllers\Skills\Models\Skill;
use App\Http\Controllers\Skills\Requests\CreateSkillRequest;
use App\Http\Controllers\Skills\Requests\DeleteSkillRequest;
use App\Http\Controllers\Skills\Requests\UpdateSkillRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class SkillsController extends Controller
{
    const MODULE_NAME = 'Skills';
    const SKILL_CREATED = 'New Skill created successfully';
    const SKILL_NOT_CREATED = 'Error in creating a Skill';
    const COLLECTION_NAME = 'Skills';
    const SKILL_UPDATED = 'Skill updated successfully';
    const SKILL_NOT_UPDATED = 'Error in updating Skill';
    const SKILL_DELETED = 'Skill deleted successfully';
    const SKILL_NOT_DELETED = 'Error in deleting Skill';
    const SKILL_ALREADY_MARKED = 'Skill already marked';
    const SKILL_NOT_FOUND = 'Skill not found';

    public function __construct(Skill $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $SkillId = $this->model->when(!empty($request->SkillId), function ($query) use ($request) {
                return $query->where('Skill-ID', $request->SkillId);
            });
              if ($SkillId) {
                  $SkillId = $this->model
                ->when(!empty($request->SkillId), function ($query) use ($request) {
                    return $query->where('Skill-ID', $request->SkillId);
                })
            ->orderBy($this->getSortBy(), $this->getSort())
            ->paginate($this->getPerPage());
                  if ($SkillId->isNotEmpty()) {
                      return $this->created([SkillsController::COLLECTION_NAME => $SkillId]);
                  }
              }
            return $this->noRecord(['message' => SkillsController::RECORD_NOT_FOUND],200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateSkillRequest $request)
    {
        try {
            $this->model->fill($request->all());
            if ($this->model->save()) {
                return $this->created(['message' => SkillsController::SKILL_CREATED]);
            }
            return $this->failed(['message' => SkillsController::SKILL_NOT_CREATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateSkillRequest $request)
    {
        try {
            $updatedSkill = $this->model::where('id', $request->id)->update($request->all());
            if ($updatedSkill) {
                return $this->created(['message' => SkillsController::SKILL_UPDATED]);
            }
            return $this->failed(['message' => SkillsController::SKILL_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteSkillRequest $request)
    {
        try {
            $this->findOneById($request->id)->delete();
            return $this->created(['message' => SkillsController::SKILL_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
    }

