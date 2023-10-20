<?php

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Jobs\Models\JobsFiles as ThisModel;
use App\Http\Controllers\Jobs\Models\JobsFiles;
use Exception;
use Illuminate\Support\Facades\Storage;
use Validator;

class JobsFilesController extends Controller
{
    const MODULE_NAME = 'Job';
    const JOB_FILE_CREATED = 'Job file added successfully';
    const JOB_FILE_NOT_CREATED = 'Error in adding a Job files';
    const COLLECTION_NAME = 'JobsFile';
    const JOB_FILE_UPDATED = 'Job file updated successfully';
    const JOB_FILE_NOT_UPDATED = 'Error in updating Job file';
    const JOB_FILE_DELETED = 'Job file deleted successfully';
    const JOB_FILE_NOT_DELETED = 'Error in deleting Job file';
    const JOB_FILE_ALREADY_MARKED = 'Job file already marked';
    const JOB_FILE_NOT_FOUND = 'Job files not found';
    const JOB_FILE_UPLOAD_ERROR = 'Job upload files not found';
    const JOB_FILE_INVALID_FORMAT = 'Job invalid upload files. only (docx, pdf, jpg, png) allowed';
    const JOB_FILE_UPLOAD_DISK = 'job';
    const JOB_FILE_UPLOAD_PATH = 'uploads/job/files';


    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }


    public function index(Request $request, $id)
    {
        
        try 
        {
            $files = JobsFiles::where('job_id', $id)->get();
            return response()->json(['data'=>$files]);
            
        } 
        catch (Exception $ex) 
        {
            return $this->serverError($ex);
        }
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request, $file = 'file', $directory = 'public/jobfile')
    {
        
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:job,id',
            'file' => 'required|file'
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        else
        {
            try {
                if ($request->hasFile($file))
                    {
                        if ($file = $request->file('file')) {
                        $extension = $request->file->getClientOriginalName();
                        $fileName  = $file . time() . md5($request->file($file)) . rand(1111111111,9999999999). '.'  . $extension;
                        if (!\Storage::exists($directory))
                        {
                            \Storage::makeDirectory($directory);
                        }
                        try
                        {
                            $path=$file->store('public/jobfile');
                        }
                        catch (\Exception $e)
                        {
                        logError(__METHOD__, __LINE__ , $e);
                        }
                        $save = new JobsFiles();
                        $save->job_id = $request->job_id;
                        $save->file_name = $extension;
                        $save->path= $path;
                        $save->save();
                        return response()->json(['message'=> "JobFiles added successfully"]);
                    }
                }
            } catch (Exception $ex) {
                return $this->serverError($ex);
            }
        }
    }

    
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        else
        {
            try {
                $file = $this->model->where('id', $request->id)->first();
                if($file){
                    $temp_file = $file->getRawOriginal('path');
                    if($file->delete()){
                        return $this->created(['message' => JobsFilesController::JOB_FILE_DELETED]);
                    }
                }else{
                    return $this->noRecord(['message' => JobsFilesController::RECORD_NOT_FOUND], 200);
                }
            } catch (Exception $ex) {
                return $this->serverError($ex);
            }
        }
    }
}
