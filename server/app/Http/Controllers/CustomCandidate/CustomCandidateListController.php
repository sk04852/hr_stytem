<?php

namespace App\Http\Controllers\CustomCandidate;

use App\Http\Controllers\CustomCandidate\Requests\AddCandidatesInListRequest;
use App\Http\Controllers\CustomCandidate\Requests\AddMultipleCandidatesMultipeListsRequest;
use App\Http\Controllers\CustomCandidate\Requests\CreateCandidateCustomListRequest;
use App\Http\Controllers\CustomCandidate\Requests\RemoveCandidatesFromListRequest;
use App\Http\Controllers\CustomCandidate\Requests\RemoveMultipleCandidatesMultipeListsRequest;
use App\Http\Controllers\CustomCandidate\Requests\UpdateCandidateCustomListRequest;
use App\Http\Controllers\CustomCandidate\Requests\DeleteCandidateCustomListRequest;
use App\Http\Controllers\CustomCandidate\Requests\UpdateCandidateCustomListNameRequest;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\CustomCandidate\Models\CustomCadidateListName;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomCandidateListController extends Controller
{
    const CANDIDATE_CUSTOM_LIST_CREATED = 'New Candidate Custom list created successfully';
    const CANDIDATE_CUSTOM_LIST_NOT_CREATED = 'Error in creating a Custom Candidate list';
    const CANDIDATE_CUSTOM_LIST_UPDATED = 'Custom Candidate List updated successfully';
    const CANDIDATE_CUSTOM_LIST_NOT_UPDATED = 'Custom Candidate List not updated';
    const CANDIDATE_CV_NOT_UPDATED = 'Error in updating CandidateCV';
    const CANDIDATE_NOT_ACTION_UPDATED = 'Candidate status update failed';
    const CUSTOM_CANDIDATE_lIST_DELETED = 'Custom Candidate List deleted successfully';
    const CUSTOM_CANDIDATE_lIST_NOT_DELETED = 'Error in deleting Custom Candidate List';
    const COSTUM_CANDIDATE_LIST_FILTER_WENT_WRONG = 'Something went wrong please try again';
    const COSTUM_CANDIDATE_LIST_Data = "Custom Candidate List";
    const CUSTOM_CANDIDATE_LIST_NOT_FOUND = 'Custom Candidate List not found';
    const ADD_CANDIDATES_IN_CUSTOM_LIST = 'Candidate added in Custom Candidate List';
    const ADD_CANDIDATES_IN_CUSTOM_LIST_FAILED = 'Unable to add Candidates to Custom Candidate Lists';
    const REMOVE_CANDIDATES_FROM_CUSTOM_LIST = 'Candidate removed from Custom Candidate List';
    const REMOVE_CANDIDATES_FROM_CUSTOM_LIST_FAILED = 'Unable to remove Candidates from Custom Candidate Lists';


    public function __construct(CustomCadidateListName $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try {
            $user_id = auth()->user()->id;
            $candidate_list = $this->model::where('user_id', $user_id)
                // ->with('candidates.action')
                ->get();
            return $this->created(['candidate_list' => $candidate_list]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }

    public function store(CreateCandidateCustomListRequest $request)
    {
        try {
            $data = null;
            DB::transaction(function () use ($request, &$data) {
                $list_name = $this->model::create([
                    'user_id' => auth()->user()->id,
                    'list_name' => $request->list_name
                ]);

                $list_name->candidates()->sync($request->candidate_ids);

                $data = $list_name;
            });

            return $this->created(['message' => CustomCandidateListController::CANDIDATE_CUSTOM_LIST_CREATED, 'data' => $data]);

        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }


    public function show(Request $request, $id)
    {
        try {
            $user_id = auth()->user()->id;
            $custom_candidate_list = $this->model::where('user_id', $user_id)
                ->where('id', $id)
                ->with('candidates.action')
                ->first();

            if (is_null($custom_candidate_list)) {
                return $this->noRecord(['message' => CustomCandidateListController::RECORD_NOT_FOUND]);

            } else {
                return $this->created(['custom_candidate_list' => $custom_candidate_list]);
            }

        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }


    public function update(UpdateCandidateCustomListRequest $request)
    {
        try {
            $custom_candidate_list = $this->model::where('id', $request->list_name_id)->first();
            if (is_null($custom_candidate_list)) {
                return $this->noRecord(['message' => CustomCandidateListController::RECORD_NOT_FOUND]);
            }

            DB::transaction(function () use ($request, $custom_candidate_list) {
                if (!is_null($request->list_name)) {
                    $custom_candidate_list->list_name = $request->list_name;
                    $custom_candidate_list->save();
                }

                $custom_candidate_list->candidates()->sync($request->candidate_ids);
            });

            return $this->created(['message' => CustomCandidateListController::CANDIDATE_CUSTOM_LIST_UPDATED]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteCandidateCustomListRequest $request)
    {
        try {
            $custom_candidate_list = $this->model::where('id', $request->list_name_id)->first();
            if (is_null($custom_candidate_list)) {
                return $this->noRecord(['message' => CustomCandidateListController::RECORD_NOT_FOUND]);
            }

            DB::transaction(function () use ($request, $custom_candidate_list) {
                $custom_candidate_list->candidates()->detach();

                $custom_candidate_list->delete();
            });

            return $this->created(['message' => CustomCandidateListController::CUSTOM_CANDIDATE_lIST_DELETED]);

        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }

    public function filter(Request $request)
    {
        try {
            if ($request->has('job_type')) {
                $data = CustomCandidateList::where('job_type', $request->job_type)->get();
                return response()->json(['data' => $data]);
            } elseif ($request->has('gender')) {
                $data = CustomCandidateList::where('gender', $request->gender)->get();
                return response()->json(['data' => $data]);
            } elseif ($request->has('location')) {
                $data = CustomCandidateList::where('location', $request->location)->get();
                return response()->json(['data' => $data]);
            } elseif ($request->has('source')) {
                $data = CustomCandidateList::where('source', $request->source)->get();
                return response()->json(['data' => $data]);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }

//    public function edit_list(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'list_id' => 'required',
//        ]);
//        if ($validator->fails()) {
//            return response(['errors' => $validator->errors()->all()], 422);
//        } else {
//            try {
//                $candidate_list_name = CustomCadidateListName::where('id', $request->list_id)->get();
//                return response()->json(['candidate_list' => $candidate_list_name]);
//            } catch (Exception $ex) {
//                return response()->json(['message' => $ex->getMessage()]);
//            }
//        }
//
//    }

    public function update_list(UpdateCandidateCustomListNameRequest $request)
    {
        try {
            $data = $this->model::where('id', $request->list_name_id)->first();
            $data->list_name = ($request->list_name) ?? $data->list_name;
            $data->save();
            return $this->created(['message' => "Custom Candidate List Name Updated Successfully"]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }

//    public function destroy_list(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'list_id' => 'required',
//        ]);
//        if ($validator->fails()) {
//            return response(['errors' => $validator->errors()->all()], 422);
//        } else {
//            try {
//                $data = CustomCadidateListName::find($request->list_id);
//                if ($data->delete()) {
//                    $custom_list = CustomCandidateList::where('list_name_id', $request->list_id)->delete();
//                    return response()->json(['message' => "Custom Candidate List Name Deleted Successfully"]);
//                } else {
//                    return response()->json(['message' => "Custom Candidate List Name Note Deleted"]);
//                }
//
//            } catch (Exception $ex) {
//                return response()->json(['message' => $ex->getMessage()]);
//            }
//
//        }
//    }

    public function addCandidatesInList (AddCandidatesInListRequest $request){
        try {
            $custom_candidate_list = $this->model::find($request->list_name_id);
            if (is_null($custom_candidate_list)) {
                return $this->noRecord(['message' => CustomCandidateListController::RECORD_NOT_FOUND]);
            }

            DB::transaction(function () use ($request, $custom_candidate_list) {
                $custom_candidate_list->candidates()->attach($request->candidate_ids);
            });

            return $this->created(['message' => CustomCandidateListController::ADD_CANDIDATES_IN_CUSTOM_LIST]);

        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }

    public function removeCandidateFromList(RemoveCandidatesFromListRequest $request){
        try {
            $custom_candidate_list = $this->model::find($request->list_name_id);
            if (is_null($custom_candidate_list)) {
                return $this->noRecord(['message' => CustomCandidateListController::RECORD_NOT_FOUND]);
            }

            DB::transaction(function () use ($request, $custom_candidate_list) {
                $custom_candidate_list->candidates()->detach($request->candidate_ids);
            });

            return $this->created(['message' => CustomCandidateListController::REMOVE_CANDIDATES_FROM_CUSTOM_LIST]);

        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }

    public function addMultipleCandidateMultipleList(AddMultipleCandidatesMultipeListsRequest $request){
        try {
            $response = false;
            DB::transaction(function () use ($request, &$response){
                $custom_lists = collect($request->custom_lists);
                $candidate_ids = $request->candidate_ids;

                if($custom_lists->isNotEmpty()){
                    foreach ($custom_lists as $custom_list){
                        if(is_null($custom_list['id'])){
                            $custom_list_object = CustomCadidateListName::create(['user_id' => Auth::id(), 'list_name' => $custom_list['name']]);
                        }else{
                            $custom_list_object = CustomCadidateListName::find($custom_list['id']);
                        }

                        if(is_null($custom_list_object)){
                            throw new ModelNotFoundException(CustomCandidateListController::CUSTOM_CANDIDATE_LIST_NOT_FOUND);
                        }


                        if(is_array($candidate_ids) && !empty($candidate_ids)){
                            foreach ($candidate_ids as $candidate_id){
                                $result = $custom_list_object->candidates->where('pivot.candidatecv_id', $candidate_id);
                                if($result->isEmpty()){
                                    $custom_list_object->candidates()->attach($candidate_id);
                                }
                            }
                        }
                    }

                    $response = true;
                }

            });

            if($response){
                return $this->created(['message' => CustomCandidateListController::ADD_CANDIDATES_IN_CUSTOM_LIST]);
            }else{
                return $this->failed(['message' => CustomCandidateListController::ADD_CANDIDATES_IN_CUSTOM_LIST_FAILED]);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }

    public function removeMultipleCandidateMultipleList(RemoveMultipleCandidatesMultipeListsRequest $request){
        try {
            $response = false;
            DB::transaction(function () use ($request, &$response){
                $custom_list_ids = $request->list_name_ids;
                $candidate_ids = $request->candidate_ids;

                if(is_array($custom_list_ids) && !empty($custom_list_ids)){
                    foreach ($custom_list_ids as $custom_list_id){
                        $custom_list = CustomCadidateListName::find($custom_list_id);
                        if(is_null($custom_list)){
                            throw new ModelNotFoundException(CustomCandidateListController::CUSTOM_CANDIDATE_LIST_NOT_FOUND);
                        }

                        if(is_array($candidate_ids) && !empty($candidate_ids)){
                            foreach ($candidate_ids as $candidate_id){
                                $result = $custom_list->candidates->where('pivot.candidatecv_id', $candidate_id);
                                if($result->isNotEmpty()){
                                    $custom_list->candidates()->detach($candidate_id);
                                }
                            }
                        }
                    }

                    $response = true;
                }

            });

            if($response){
                return $this->created(['message' => CustomCandidateListController::REMOVE_CANDIDATES_FROM_CUSTOM_LIST]);
            }else{
                return $this->failed(['message' => CustomCandidateListController::REMOVE_CANDIDATES_FROM_CUSTOM_LIST_FAILED]);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }
}
