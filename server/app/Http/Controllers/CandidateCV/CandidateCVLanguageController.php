<?php

namespace App\Http\Controllers\CandidateCV;

use App\Events\Timeline\CandidateCV\CandidateLanguageUpdated;
use App\Events\Timeline\CandidateCV\CandidateLanguageCreated;
use App\Events\Timeline\CandidateCV\CandidateLanguageDeleted;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\Languages\Models\Language;
use App\Http\Controllers\CandidateCV\Requests\CreateCandidateLanguageRequest;
use App\Http\Controllers\CandidateCV\Requests\DeleteCandidateLanguageRequest;
use App\Http\Controllers\CandidateCV\Requests\UpdateCandidateLanguageRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Timelines\Models\Timeline;
use App\Http\Controllers\Timelines\Traits\TimelineTrait;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CandidateCVLanguageController extends Controller
{
    use TimelineTrait;

    const MODULE_NAME = 'CandidateCV';
    const CANDIDATE_LANGUAGE_CREATED = 'Candidate Language added successfully';
    const CANDIDATE_LANGUAGE_NOT_CREATED = 'Error in adding a Candidate Language';
    const COLLECTION_NAME = 'CandidateCVLanguages';
    const CANDIDATE_LANGUAGE_UPDATED = 'Candidate Language updated successfully';
    const CANDIDATE_LANGUAGE_NOT_UPDATED = 'Error in updating Candidate Language';
    const CANDIDATE_LANGUAGE_DELETED = 'Candidate Language deleted successfully';
    const CANDIDATE_LANGUAGE_NOT_DELETED = 'Error in deleting Candidate Language';
    const CANDIDATE_LANGUAGE_ALREADY_MARKED = 'Candidate Language already marked';
    const CANDIDATE_LANGUAGE_NOT_FOUND = 'Candidate Language not found';
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
                $languages = $Candidatecv->languages;
                if ($languages->isNotEmpty()) {
                    return $this->created([CandidateCVLanguageController::COLLECTION_NAME => $languages->toArray()]);
                }
            }
            return $this->noRecord(['message' => CandidateCVLanguageController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function store(CreateCandidateLanguageRequest $request)
    {
        try {
            $candidatecv = $this->model->where('id', $request->candidatecv_id)->first();
            if (is_null($candidatecv)) {
                return $this->failed(['message' => CandidateCVLanguageController::CANDIDATE_NOT_FOUND]);
            }

            DB::transaction(function () use ($request, $candidatecv) {
                CandidateCV::disableSearchSyncing();
                $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
                $languages = $request->language;
                if (is_array($languages) && !empty($languages)) {
                    foreach ($languages as $language) {
                        $temp_language = Language::firstOrCreate(['name' => $language['name']]);
                        if (is_null($temp_language)) {
                            throw new \Exception(CandidateCVLanguageController::CANDIDATE_LANGUAGE_NOT_CREATED);
                        }

                        $candidatecv->languages()->attach($temp_language->id, [
                            'level' => isset($language['level']) ? $language['level'] : null,
                            'speaking' => isset($language['speaking']) ? $language['speaking'] : null,
                            'reading_writing' => isset($language['reading_writing']) ? $language['reading_writing'] : null
                        ]);
                        $this->recordOneToManyNew('languages', $temp_language->name, $timeline_data);
                    }
                }

                $candidatecv->searchable();
                CandidateLanguageCreated::dispatch($candidatecv, 'Candidate Language Created', $timeline_data['old_values'], $timeline_data['new_values']);
            });

            CandidateCV::enableSearchSyncing();
            return $this->created([
                'message' => CandidateCVLanguageController::CANDIDATE_LANGUAGE_CREATED,
                'data' => $candidatecv->toArray()
            ]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function update(UpdateCandidateLanguageRequest $request)
    {
        try {
            CandidateCV::disableSearchSyncing();
            $arranged_languages = [];
            $input_languages = $request->language;
            if (is_array($input_languages) && !empty($input_languages)) {
                $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
                foreach ($input_languages as $language) {
                    $arranged_languages[$language['id']] = [
                        'level' => isset($language['level']) ? $language['level'] : null,
                        'speaking' => isset($language['speaking']) ? $language['speaking'] : null,
                        'reading_writing' => isset($language['reading_writing']) ? $language['reading_writing'] : null
                    ];
                }
            }
            $Candidatecv = $this->model->where('id', $request->candidatecv_id)->first();
            $response = $Candidatecv->languages()->sync($arranged_languages);
            $this->recordSyncChanges($response, $Candidatecv, 'languages', 'name', $timeline_data);
            if ($response) {
                $Candidatecv->searchable();
                CandidateLanguageUpdated::dispatch($Candidatecv, 'Candidate Language Updated', $timeline_data['old_values'], $timeline_data['new_values']);

                CandidateCV::enableSearchSyncing();
                return $this->created([
                    'message' => CandidateCVLanguageController::CANDIDATE_LANGUAGE_UPDATED,
                    'data' => $Candidatecv->toArray()
                ]);
            }
            CandidateCV::enableSearchSyncing();
            return $this->failed(['message' => CandidateCVLanguageController::CANDIDATE_LANGUAGE_NOT_UPDATED]);
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteCandidateLanguageRequest $request)
    {
        try {
            $response = false;

            DB::transaction(function () use ($request, &$response) {
                CandidateCV::disableSearchSyncing();
                $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
                $candidatecv = $this->model::find($request->candidatecv_id);

                $languages =  Language::whereIn('id', $request->language_ids)->get();
                if ($languages->isNotEmpty()) {
                    foreach ($languages as $language) {
                        $candidatecv->languages()->detach($language);
                        $this->recordOneToManyDelete($language, 'languages', 'name', $timeline_data);
                    }
                }

                $candidatecv->searchable();
                CandidateLanguageDeleted::dispatch($candidatecv, 'Candidate Language Deleted', $timeline_data['old_values'], $timeline_data['new_values']);

                $response = true;
            });

            if ($response) {
                CandidateCV::enableSearchSyncing();
                return $this->created(['message' => CandidateCVLanguageController::CANDIDATE_LANGUAGE_DELETED]);
            } else {
                CandidateCV::enableSearchSyncing();
                return $this->created(['message' => CandidateCVLanguageController::CANDIDATE_LANGUAGE_NOT_DELETED]);
            }
        } catch (Exception $ex) {
            CandidateCV::enableSearchSyncing();
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
}
