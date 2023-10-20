<?php

namespace App\Http\Controllers\CandidateCV;

use App\Events\Timeline\CandidateCV\CandidateEducationCreated;
use App\Events\Timeline\CandidateCV\CandidateEducationUpdated;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\CandidateCV\Models\CandidateCVEducation;
use App\Http\Controllers\CandidateCV\Requests\CreateEducationRequest;
use App\Http\Controllers\CandidateCV\Requests\DeleteEducationRequest;
use App\Http\Controllers\CandidateCV\Requests\UpdateEducationRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EducationDegrees\Models\EducationDegree;
use App\Http\Controllers\EducationDegrees\Models\EducationLevel;
use App\Http\Controllers\Timelines\Models\Timeline;
use App\Http\Controllers\Timelines\Traits\TimelineTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CandidateCVEducationController extends Controller
{
    use TimelineTrait;

    const MODULE_NAME = 'CandidateCV';
    const EDUCATION_CREATED = 'New Education created successfully';
    const EDUCATION_NOT_CREATED = 'Error in creating a Education';
    const COLLECTION_NAME = 'CandidateCVEducation';
    const EDUCATION_UPDATED = 'Education updated successfully';
    const EDUCATION_NOT_UPDATED = 'Error in updating Education';
    const EDUCATION_DELETED = 'Education deleted successfully';
    const EDUCATION_NOT_DELETED = 'Error in deleting Education';
    const EDUCATION_ALREADY_MARKED = 'Education already marked';
    const EDUCATION_NOT_FOUND = 'Education not found';

    public function __construct(CandidateCVEducation $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request, $id)
    {
        try {
            $Education = $this->model->when(!empty($id), function ($query) use ($id) {
                return $query->where('candidatecv_id', $id);
            });
            if ($Education) {
                $Education = $this->model
                    ->orderBy($this->getSortBy(), $this->getSort())
                    ->paginate($this->getPerPage())->where('candidatecv_id', $id);
                if ($Education->isNotEmpty()) {
                    return $this->created([CandidateCVEducationController::COLLECTION_NAME => $Education]);
                }
            }
            return $this->noRecord(['message' => CandidateCVEducationController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateEducationRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                CandidateCV::disableSearchSyncing();
                $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
                $education_histories = $request->get('education');

                $candidatecv = CandidateCV::find($education_histories[0]['candidatecv_id']);
                if(is_null($candidatecv)){
                    throw new ModelNotFoundException('Candidate Not Found');
                }

                if (is_array($education_histories) && !empty($education_histories)) {
                    foreach ($education_histories as $education_history) {
                        // $temp_degree = EducationDegree::firstOrCreate(['name' => $education_history['degree']]);
                        // if(is_null($temp_degree)){
                        //     throw new \Exception(CandidateCVEducationController::EDUCATION_NOT_CREATED);
                        // }
                        // unset($education_history['degree']);
                        // $education_history['degree_id'] = $temp_degree->id;

                        $education_object = CandidateCVEducation::create($education_history);
                        if(!$education_object instanceof  CandidateCVEducation){
                            throw new \Exception(CandidateCVEducationController::EDUCATION_NOT_CREATED);
                        }
                        

                        // $this->recordOneToManyNew('education', $temp_degree->name, $timeline_data);
                        $this->recordOneToManyNew('education', $education_object->institute, $timeline_data);
                    }
                }

                $candidatecv->searchable();
                CandidateEducationCreated::dispatch($candidatecv, 'Education Added', $timeline_data['old_values'], $timeline_data['new_values']);
                CandidateCV::enableSearchSyncing();
            });

            CandidateCV::enableSearchSyncing();
            return $this->created(['message' => CandidateCVEducationController::EDUCATION_CREATED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }


//    public function store(CreateEducationRequest $request)
//    {
//        try {
//            $this->model->fill($request->all());
//            if ($this->model->save()) {
//                return $this->created([
//                    'message' => CandidateCVEducationController::EDUCATION_CREATED,
//                    'data' => $this->model->toArray()
//                ]);
//            }
//            return $this->failed(['message' => CandidateCVEducationController::EDUCATION_NOT_CREATED]);
//        } catch (Exception $ex) {
//            return $this->serverError($ex);
//        }
//    }

    public function update(UpdateEducationRequest $request)
    {
        try {
            $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
            $timeline_relations_data = [
                'level_id' => ['relation' => EducationLevel::class, 'identity_column' => 'id', 'record_column_name' => 'name']
            ];

            $updatedEducation = $this->model::find($request->id);
            $updatedEducation->fill($request->except('id'));
            if($updatedEducation->isDirty()){
                $this->recordOneToManyUpdateWithRelation($updatedEducation, 'education', $timeline_data, $timeline_relations_data);
            }
            if ($updatedEducation->save()) {
                $updatedEducation->candidatecv->searchable();
                CandidateEducationUpdated::dispatch($updatedEducation->candidatecv, 'Education Updated', $timeline_data['old_values'], $timeline_data['new_values']);
                CandidateCV::enableSearchSyncing();
                return $this->created([
                    'message' => CandidateCVEducationController::EDUCATION_UPDATED,
                    'data' => $this->model->toArray()
                ]);
            }
            CandidateCV::enableSearchSyncing();
            return $this->failed(['message' => CandidateCVEducationController::EDUCATION_NOT_UPDATED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteEducationRequest $request)
    {
        try {
            CandidateCV::disableSearchSyncing();
            $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
            // $timeline_relations_data = [
            //     'level_id' => ['relation' => EducationLevel::class, 'identity_column' => 'id', 'record_column_name' => 'name']
            // ];


            $education_object = $this->model::find($request->id);
            $candidatecv= $education_object->candidatecv;
            $this->recordOneToManyDelete($education_object, 'education', 'institute', $timeline_data);
            if($education_object->delete()){
                $candidatecv->searchable();
                CandidateEducationUpdated::dispatch($candidatecv, 'Education Deleted', $timeline_data['old_values'], $timeline_data['new_values']);
                CandidateCV::enableSearchSyncing();
                return $this->created(['message' => CandidateCVEducationController::EDUCATION_DELETED]);
            }
            CandidateCV::enableSearchSyncing();
            return $this->created(['message' => CandidateCVEducationController::EDUCATION_NOT_DELETED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
}
