<?php

namespace App\Http\Controllers\CandidateCV;

use App\Events\Timeline\CandidateCV\CandidateAdditionalCourseCreated;
use App\Events\Timeline\CandidateCV\CandidateAdditionalCourseDeleted;
use App\Events\Timeline\CandidateCV\CandidateAdditionalCourseUpdated;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\CandidateCV\Models\CandidateCVAdditionalCourse;
use App\Http\Controllers\CandidateCV\Requests\CreateAdditionalCoursesRequest;
use App\Http\Controllers\CandidateCV\Requests\DeleteAdditionalCourseRequest;
use App\Http\Controllers\CandidateCV\Requests\UpdateAdditionalCourseRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Timelines\Models\Timeline;
use App\Http\Controllers\Timelines\Traits\TimelineTrait;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CandidateCVAdditionalCoursesController extends Controller
{
    use TimelineTrait;

    const MODULE_NAME = 'CandidateCV';
    const ADDITIONAL_COURSES_CREATED = 'Additional Course created successfully';
    const ADDITIONAL_COURSES_NOT_CREATED = 'Error in creating a additional course';
    const COLLECTION_NAME = 'CandidateCVAdditionalCourses';
    const ADDITIONAL_COURSES_UPDATED = 'Additional Course updated successfully';
    const ADDITIONAL_COURSES_NOT_UPDATED = 'Error in updating additional course';
    const ADDITIONAL_COURSES_DELETED = 'Additional Course deleted successfully';
    const ADDITIONAL_COURSES_NOT_DELETED = 'Error in deleting Additional Course';
    const ADDITIONAL_COURSES_ALREADY_MARKED = 'Additional Course already marked';
    const ADDITIONAL_COURSES_NOT_FOUND = 'Additional Courses not found';
    const CANDIDATE_NOT_FOUND = 'Candidate not found';

    public function __construct(CandidateCVAdditionalCourse $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request, $id)
    {
        try {
            $additional_courses = $this->model->when(!empty($id), function ($query) use ($id) {
                return $query->where('candidatecv_id', $id);
            });
            if ($additional_courses) {
                $additional_courses = $this->model
                    ->orderBy($this->getSortBy(), $this->getSort())
                    ->paginate($this->getPerPage())->where('candidatecv_id', $id);
                if ($additional_courses->isNotEmpty()) {
                    return $this->created([CandidateCVAdditionalCoursesController::COLLECTION_NAME => $additional_courses]);
                }
            }
            return $this->noRecord(['message' => CandidateCVAdditionalCoursesController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }


    public function store(CreateAdditionalCoursesRequest $request)
    {
        try {
            $candidatecv = CandidateCV::where('id', $request->candidatecv_id)->first();
            if (is_null($candidatecv)) {
                return $this->failed(['message' => CandidateCVAdditionalCoursesController::CANDIDATE_NOT_FOUND]);
            }

            DB::transaction(function () use ($request, $candidatecv) {
                CandidateCV::disableSearchSyncing();
                $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
                $courses = $request->get('courses');

                if (is_array($courses) && !empty($courses)) {
                    foreach ($courses as $course) {
                        $course['candidatecv_id'] = $candidatecv->id;
                        if(!CandidateCVAdditionalCourse::create($course) instanceof  CandidateCVAdditionalCourse){
                            throw new \Exception(CandidateCVAdditionalCoursesController::ADDITIONAL_COURSES_NOT_CREATED);
                        }

                        $this->recordOneToManyNew('additionalCourses', $course['description'], $timeline_data);
                    }
                }

                $candidatecv->searchable();
                CandidateAdditionalCourseCreated::dispatch($candidatecv, 'Additional Course Added', $timeline_data['old_values'], $timeline_data['new_values']);
            });

            CandidateCV::enableSearchSyncing();
            return $this->created(['message' => CandidateCVAdditionalCoursesController::ADDITIONAL_COURSES_CREATED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }


    public function update(UpdateAdditionalCourseRequest $request)
    {
        try {
            CandidateCV::disableSearchSyncing();
            $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
            $additional_course = $this->model::find($request->id);
            if(is_null($additional_course)){
                return $this->failed(['message' => CandidateCVAdditionalCoursesController::RECORD_NOT_FOUND]);
            }
            $additional_course->fill($request->except('id'));
            if($additional_course->isDirty()){
                $this->recordOneToManyUpdate($additional_course, 'additionalCourses', $timeline_data);
            }

            if ($additional_course->save()){
                $additional_course->candidatecv->searchable();
                CandidateAdditionalCourseUpdated::dispatch($additional_course->candidatecv, 'Additional Course Updated', $timeline_data['old_values'], $timeline_data['new_values']);
                CandidateCV::enableSearchSyncing();
                return $this->created([
                    'message' => CandidateCVAdditionalCoursesController::ADDITIONAL_COURSES_UPDATED,
                    'data' => $this->model->toArray()
                ]);
            }
            CandidateCV::enableSearchSyncing();
            return $this->failed(['message' => CandidateCVAdditionalCoursesController::ADDITIONAL_COURSES_NOT_UPDATED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteAdditionalCourseRequest $request)
    {
        try {
            CandidateCV::disableSearchSyncing();
            $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
            $additional_course = $this->model::find($request->id);
            $candidatecv = $additional_course->candidatecv;
            $this->recordOneToManyDelete($additional_course, 'additionalCourses', 'description', $timeline_data);
            if($additional_course->delete()){
                $candidatecv->searchable();
                CandidateAdditionalCourseDeleted::dispatch($candidatecv, 'Additional Course Deleted', $timeline_data['old_values'], $timeline_data['new_values']);
                CandidateCV::enableSearchSyncing();
                return $this->created(['message' => CandidateCVAdditionalCoursesController::ADDITIONAL_COURSES_DELETED]);
            }

            CandidateCV::enableSearchSyncing();
            return $this->failed(['message' => CandidateCVAdditionalCoursesController::ADDITIONAL_COURSES_NOT_DELETED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
}
