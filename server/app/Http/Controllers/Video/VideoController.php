<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Video\Models\Video as ThisModel;
use App\Http\Controllers\Video\Models\Video;
use Exception;
use Illuminate\Support\Facades\Storage;
use Validator;
use URL;
use Carbon\Carbon;

class VideoController extends Controller
{

    const VIDEO_UPLOAD_DISK = 'video';
    const VIDEO_UPLOAD_PATH = 'uploads/videos';
    const VIDEO_CREATED = 'Video or Link added successfully';
    const VIDEO_FILE_NOT_CREATED = 'Error in adding a Video files or Links';
    
    public function __construct(ThisModel $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        try 
        {
            $data = Video::all();
            return response()->json(['data' => $data]);
        }  
        catch (Exception $ex) {
            return response()->json(['message'=>$ex->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $file = 'file', $directory = 'public/video')
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
                            $path=$file->store('public/video');
                        }
                        catch (\Exception $e)
                        {
                        logError(__METHOD__, __LINE__ , $e);
                        }
                        $save = new Video();
                        $save->file_name = $extension;
                        $save->path= $path;
                        $save->video_link = $request->link;
                        $save->save();
                        return response()->json(['message'=> "Video or Link added successfully"]);
                    }
                }
                else 
                {
                    $save = new Video();
                    $save->video_link = $request->link;
                    $save->save();
                    return response()->json(['message'=> "Video or Link added successfully"]);
                }
                
            }
            catch (Exception $ex) {
                return response()->json(['message'=>$ex->getMessage()]);
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
            try 
            {
                $data = Video::find($request->id);
                // return $data;
                if($data)
                {
                    if ($data->delete()) {
                        return response()->json(['message'=> 'Record Deleted Successfully']);
                    }
                }
                else
                {
                    return response()->json(['message'=> 'Record Not Found']);
                }
            } 
            catch (Exception $ex) {
                return $this->serverError($ex);
            }
        }
    }
}
