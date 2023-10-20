<?php

namespace App\Http\Controllers\CandidateCV;

use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\Skills\Models\Skill;
use App\Http\Controllers\CandidateCV\Requests\CreateCandidateSkillRequest;
use App\Http\Controllers\CandidateCV\Requests\DeleteCandidateSkillRequest;
use App\Http\Controllers\CandidateCV\Requests\UpdateCandidateSkillRequest;
use App\Http\Controllers\Controller;
use App\Models\History;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class CandidateCVSkillController extends Controller
{
    const MODULE_NAME = 'CandidateCV';
    const CANDIDATE_SKILL_CREATED = 'Candidate Skills added successfully';
    const CANDIDATE_SKILL_NOT_CREATED = 'Error in adding a Candidate Skills';
    const COLLECTION_NAME = 'CandidateCVSkills';
    const CANDIDATE_SKILL_UPDATED = 'Candidate Skills updated successfully';
    const CANDIDATE_SKILL_NOT_UPDATED = 'Error in updating Candidate Skills';
    const CANDIDATE_SKILL_DELETED = 'Candidate Skills deleted successfully';
    const CANDIDATE_SKILL_NOT_DELETED = 'Error in deleting Candidate Skills';
    const CANDIDATE_SKILL_ALREADY_MARKED = 'Candidate Skills already marked';
    const CANDIDATE_SKILL_NOT_FOUND = 'Candidate Skills not found';
    const CANDIDATE_NOT_FOUND = 'Candidate not found';

    public function __construct(CandidateCV $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request, $id)
    {
        try {
            $Candidatecv = $this->model->where('id', $id)->first();
            if ($Candidatecv) {
                $Skills = $Candidatecv->skills;
                if ($Skills->isNotEmpty()) {
                    return $this->created([CandidateCVSkillController::COLLECTION_NAME => $Skills->toArray()]);
                }
            }
            return $this->noRecord(['message' => CandidateCVSkillController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateCandidateSkillRequest $request)
    {
        try {
            $candidatecv = $this->model->where('id', $request->candidatecv_id)->first();
            if (is_null($candidatecv)) {
                return $this->failed(['message' => CandidateCVSkillController::CANDIDATE_NOT_FOUND]);
            }

            DB::transaction(function () use ($request, $candidatecv) {
                $skills = $request->skills;
                if (is_array($skills) && !empty($skills)) {
                    foreach ($skills as $skill) {
                        $temp_skill = Skill::firstOrCreate(['name' => $skill]);
                        if (is_null($temp_skill)) {
                            throw new \Exception(CandidateCVSkillController::CANDIDATE_SKILL_NOT_CREATED);
                        }

                        $candidatecv->skills()->attach($temp_skill->id);
//                        History::create([
//                            'reference_table' => 'candidatecv_skill',
//                            'reference_id' => $request->candidatecv_id,
//                            'actor_id' => auth()->user()->id,
//                            'body' => "Added candidate skill {$temp_skill->name}",
//                        ]);
                    }
                }
            });

            return $this->created([
                'message' => CandidateCVSkillController::CANDIDATE_SKILL_CREATED,
                'data' => $candidatecv->toArray()
            ]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

//    public function store(CreateCandidateSkillRequest $request)
//    {
//        try {
//            $Candidatecv = $this->model->where('id', $request->candidatecv_id)->first();
//            $response = $Candidatecv->skills()->sync($request->skill_ids);
//            \Log::info($request->skill_ids);
//            if ($response) {
//                foreach ($request->skill_ids as $item) {
//                    $name = Skill::where('id', $item)->first()->name;
//                    History::create([
//                        'reference_table' => 'candidatecv_skill',
//                        'reference_id'    => $request->candidatecv_id,
//                        'actor_id'        => auth()->user()->id,
//                        'body'            => "Added candidate skill {$name}",
//                    ]);
//                }
//                return $this->created([
//                    'message' => CandidateCVSkillController::CANDIDATE_SKILL_CREATED,
//                    'data' => $Candidatecv->toArray()
//                ]);
//            }
//            return $this->failed(['message' => CandidateCVSkillController::CANDIDATE_SKILL_NOT_CREATED]);
//        } catch (Exception $ex) {
//            return $this->serverError($ex);
//        }
//    }

    public function update(UpdateCandidateSkillRequest $request)
    {
        try {
            $Candidatecv = $this->model->where('id', $request->candidatecv_id)->first();
            $response = $Candidatecv->skills()->sync($request->skill_ids);
            if ($response) {
//                foreach ($request->skill_ids as $item) {
//                    $name = Skill::where('id', $item)->first()->name;
//                    History::create([
//                        'reference_table' => 'candidatecv_skill',
//                        'reference_id' => $request->candidatecv_id,
//                        'actor_id' => auth()->user()->id,
//                        'body' => "Updated candidate skill {$name}",
//                    ]);
//                }
                return $this->created([
                    'message' => CandidateCVSkillController::CANDIDATE_SKILL_UPDATED,
                    'data' => $Candidatecv->toArray()
                ]);
            }
            return $this->failed(['message' => CandidateCVSkillController::CANDIDATE_SKILL_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteCandidateSkillRequest $request)
    {
        try {
            $Candidatecv = $this->model->where('id', $request->candidatecv_id)->first();
            $response = $Candidatecv->skills()->detach($request->skill_ids);
            return $this->created(['message' => CandidateCVSkillController::CANDIDATE_SKILL_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

