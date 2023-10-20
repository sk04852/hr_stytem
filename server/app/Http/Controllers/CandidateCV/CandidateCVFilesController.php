<?php

namespace App\Http\Controllers\CandidateCV;

use App\Events\Timeline\CandidateCV\CandidateFileCreated;
use App\Events\Timeline\CandidateCV\CandidateFileDeleted;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\CandidateCV\Models\CandidateCVFiles;
use App\Http\Controllers\CandidateCV\Requests\CreateFileRequest;
use App\Http\Controllers\CandidateCV\Requests\DeleteFileRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Timelines\Models\Timeline;
use App\Http\Controllers\Timelines\Traits\TimelineTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CandidateCVFilesController extends Controller
{
    use TimelineTrait;

    const MODULE_NAME = 'CandidateCV';
    const CANDIDATE_FILES_CREATED = 'Candidate file added successfully';
    const CANDIDATE_FILES_NOT_CREATED = 'Error in adding a Candidate files';
    const COLLECTION_NAME = 'CandidateCVFiles';
    const CANDIDATE_FILES_UPDATED = 'Candidate file updated successfully';
    const CANDIDATE_FILES_NOT_UPDATED = 'Error in updating Candidate file';
    const CANDIDATE_FILES_DELETED = 'Candidate file deleted successfully';
    const CANDIDATE_FILES_NOT_DELETED = 'Error in deleting Candidate file';
    const CANDIDATE_FILES_ALREADY_MARKED = 'Candidate file already marked';
    const CANDIDATE_FILES_NOT_FOUND = 'Candidate files not found';
    const CANDIDATE_FILES_UPLOAD_ERROR = 'Candidate upload files not found';
    const CANDIDATE_FILES_INVALID_FORMAT = 'Candidate invalid upload files. only (docx, doc, txt, xls, csv, zip, pdf, jpg, jpeg, png, zip) allowed';
    const CANDIDATE_FILES_UPLOAD_DISK = 'candidatecv';
    const CANDIDATE_FILES_UPLOAD_PATH = 'uploads/candidatecv/files';

    public function __construct(CandidateCVFiles $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request, $id)
    {
        try {
            $files = $this->model->where('candidatecv_id', $id)->with('userPr.user')->get();
            if ($files->isNotEmpty()) {
                $data = $files->toArray();
                foreach ($files as $key =>  $file){
                    $data[$key]['size'] = Storage::disk(CandidateCVFilesController::CANDIDATE_FILES_UPLOAD_DISK)->size($file->getRawOriginal('path'));
                }
                return $this->created([CandidateCVFilesController::COLLECTION_NAME => $data]);
            }else{
                return $this->noRecord(['message' => CandidateCVFilesController::RECORD_NOT_FOUND], 200);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }

    }

    public function store(CreateFileRequest $request)
    {

        try {
            $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
            if (!$request->hasFile('files')) {
                return $this->failed(['message' => CandidateCVFilesController::CANDIDATE_FILES_UPLOAD_ERROR]);
            }

            $allowedfileExtension = ['docx', 'doc', 'txt', 'xls', 'xlsx', 'csv', 'zip', 'pdf', 'PDF', 'jpg', 'JPG', 'jpeg', 'png', 'zip'];
            $files = $request->file('files');
            $errors = [];

            foreach ($files as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                $check = in_array($extension, $allowedfileExtension);

                if ($check) {
                    $candidatecv = CandidateCV::find($request->candidatecv_id);
                    if(is_null($candidatecv)){
                        throw new ModelNotFoundException('Candidate not found');
                    }

                    $data['candidatecv_id'] = $candidatecv->id;
                    $data['path'] = $file->store(CandidateCVFilesController::CANDIDATE_FILES_UPLOAD_PATH, CandidateCVFilesController::CANDIDATE_FILES_UPLOAD_DISK);
                    $data['file_name'] = $file->getClientOriginalName();
                    $data['user_pr_id'] = Auth::id();
                    $this->model->create($data);
                    $this->recordOneToManyNew('files', $data['file_name'], $timeline_data);

                    CandidateFileCreated::dispatch($candidatecv, 'Candidate File Created', $timeline_data['old_values'], $timeline_data['new_values']);

                } else {
                    return $this->failed(['message' => CandidateCVFilesController::CANDIDATE_FILES_INVALID_FORMAT]);
                }
            }
            return $this->created([
                'message' => CandidateCVFilesController::CANDIDATE_FILES_CREATED,
                'data' => $this->model->toArray()
            ]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteFileRequest $request)
    {

        try {
            $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
            $file = $this->model->where('id', $request->id)->first();
            if($file){
                $temp_file = $file->getRawOriginal('path');
                if($file->delete()){
                    Storage::disk(CandidateCVFilesController::CANDIDATE_FILES_UPLOAD_DISK)->delete($temp_file);
                    $this->recordOneToManyDelete($file, 'files', 'file_name', $timeline_data);
                    CandidateFileDeleted::dispatch($file->candidatecv, 'Candidate File Deleted', $timeline_data['old_values'], $timeline_data['new_values']);
                    return $this->created(['message' => CandidateCVFilesController::CANDIDATE_FILES_DELETED]);
                }
            }else{
                return $this->noRecord(['message' => CandidateCVFilesController::RECORD_NOT_FOUND], 200);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }
}

