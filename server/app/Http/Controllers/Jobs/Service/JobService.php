<?php
/**
 * JobService
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\Jobs\Service;

use App\Events\InvalidateUrl;
use App\Events\Timeline\Company\CompanyConfirmedJob;
use App\Events\Timeline\Job\DeleteJobVideo;
use App\Events\Timeline\Job\JobCreated;
use App\Events\Timeline\Job\JobDeleted;
use App\Events\Timeline\Job\JobUpdated;
use App\Http\Controllers\Companies\Models\Company;
use App\Http\Controllers\Companies\Models\CompanyPr;
use App\Http\Controllers\Documents\Requests\DeleteDocumentRequest;
use App\Http\Controllers\Jobs\Models\Job;
use App\Http\Controllers\Jobs\Models\JobPr;
use App\Http\Controllers\Jobs\Models\JobsShift;
use App\Http\Controllers\Jobs\Models\JobsVideo;
use App\Http\Controllers\Jobs\Requests\CreateJobLinkRequest;
use App\Http\Controllers\Jobs\Requests\CreateJobRequest;
use App\Http\Controllers\Jobs\Requests\UpdateJobRequest;
use App\Http\Controllers\Languages\Models\Language;
use App\Http\Controllers\Timelines\Models\Timeline;
use App\Http\Controllers\Timelines\Traits\TimelineTrait;
use App\Models\UserPr;
use App\Notifications\CompanyConfirmedJob as NotificationsCompanyConfirmedJob;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class JobService
{
    use TimelineTrait;

    const JOB_NOT_CREATED = 'Error in creating a Job';

    const JOB_FILES_UPLOAD_ERROR = 'Job upload files not found';
    const JOB_FILES_INVALID_FORMAT = 'Job invalid upload files. only (docx, pdf, jpg, png) allowed';
    const JOB_VIDEOS_INVALID_FORMAT = 'Job invalid upload files. only (mov, mp4, 3gp) allowed';
    const JOB_FILES_UPLOAD_DISK = 'job';
    const JOB_FILES_UPLOAD_PATH = 'uploads/job/files';
    const JOB_VIDEOS_UPLOAD_PATH = 'uploads/job/videos';

    public function createJob(FormRequest $request){
        $response = null;
        $data['job_pr'] = $request->only([
            'company_pr_id',
            "status",
            'deadline',
            "desired_start_time",
            "required_candidates",
            "creator",
            "contact_name",
            "contact_email",
            "contact_number",
            "training",
            "observation",
            "desired_language_comment",
            "salary",
            "salary_type",
            "salary_amount_1",
            "salary_amount_2",
            "job_type",
            "duration_type",
            "employment_type",
            "job_type_comment",
            "transport",
            "transport_comment",
            "working_hours",
            "working_hours_comment",
            "clothes",
            "clothes_comment",
            'shifts'
        ]);

        $data['job'] = $request->only([
            'offer_name',
            'title',
            "benefits",
            "location",
            "department",
            'description',
            "requirements",
            "comments",
            "additional_information",
            'recess'
        ]);

        DB::transaction(function () use ($request, $data, &$response){
            JobPr::disableSearchSyncing();
            $company_pr = CompanyPr::with('company')->findOrFail($data['job_pr']['company_pr_id']); 
            $data['job_pr']['creator'] = $company_pr->company[0]->name;   

            if(isset($data['job_pr'])){
                $job_pr_object = JobPr::create($data['job_pr']);
            }else{
                throw new Exception(self::JOB_NOT_CREATED, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $job_pr_object->jobs()->create($data['job']);


            $job_pr_object->workinglanguages()->sync($request->get('work_language'));

            $desired_languages = $request->get('desired_language');
            if(!is_null($desired_languages) && is_array($desired_languages) && !empty($desired_languages)){
                foreach ($desired_languages as $language){
                    if(!is_null($language)){
                        $job_pr_object->desiredlanguages()->attach($language);
                    }
                }
            }

            $shifts_data = $request->get('shifts_data');
            if(!is_null($shifts_data) && is_array($shifts_data) && !empty($shifts_data)){
                foreach ($shifts_data as $shit){
                    $job_pr_object->shifts()->create($shit);
                }
            }


            if ($request->hasFile('files')) {
                $allowedfileExtension = ['docx', 'xls', 'csv', 'zip', 'pdf', 'PDF', 'jpg', 'JPG', 'jpeg', 'png'];
                $files = $request->file('files');

                foreach ($files as $file) {
                    if(!is_null($file)){
                        $extension = strtolower($file->getClientOriginalExtension());
                        $check = in_array($extension, $allowedfileExtension);
    
                        if ($check) {
                            $data['path'] = $file->store(self::JOB_FILES_UPLOAD_PATH, self::JOB_FILES_UPLOAD_DISK);
                            $data['file_name'] = $file->getClientOriginalName();
                            $job_pr_object->files()->create($data);
                        } else {
                            throw new Exception(self::JOB_FILES_INVALID_FORMAT, Response::HTTP_UNPROCESSABLE_ENTITY);
                        }
                    }
                }
            }


            if ($request->has('video')) {
                $videos = $request->get('video');
                $video_files = $request->file('video');
                if(is_array($videos) && !empty($videos)){
                    foreach ($videos as $key => $video){
                        if($video['type']  == (int)2){
                            $job_pr_object->videos()->create([
                                'type' => JobsVideo::VIDEO_TYPE_LINK,
                                'link' => $this->generateVideoEmbedUrl($video['link'])
                            ]);
                        }
                        if($video['type']  == (int)1){
                            $allowedfileExtension = ['mov', 'mp4', '3gp', 'MP4', 'MOV', 'AVI', 'MKV', 'avi', 'mkv'];
                            if(isset($video_files[$key]['file'])){
                                $extension = strtolower($video_files[$key]['file']->getClientOriginalExtension());
                                $check = in_array($extension, $allowedfileExtension);
                                if ($check) {
                                    $data['type'] = JobsVideo::VIDEO_TYPE_FILE;
                                    $data['path'] = $video_files[$key]['file']->store(self::JOB_VIDEOS_UPLOAD_PATH, self::JOB_FILES_UPLOAD_DISK);
                                    $data['file_name'] = $video_files[$key]['file']->getClientOriginalName();
                                    $job_pr_object->videos()->create($data);
                                } else {
                                    throw new Exception(self::JOB_VIDEOS_INVALID_FORMAT, Response::HTTP_UNPROCESSABLE_ENTITY);
                                }
                            }
                        }
                    }
                }
            }

            $job_pr_object->searchable();
            JobCreated::dispatch($job_pr_object, 'Job Created');

            $response = $job_pr_object;
        });

        JobPr::enableSearchSyncing();
        return $response;
    }

    public function generateVideoEmbedUrl($url){
        //This is a general function for generating an embed link of an FB/Vimeo/Youtube Video.
        $finalUrl = '';
        if(strpos($url, 'facebook.com/') !== false) {
            //it is FB video
            $finalUrl.='https://www.facebook.com/plugins/video.php?href='.rawurlencode($url).'&show_text=1&width=200';
        }else if(strpos($url, 'vimeo.com/') !== false) {
            //it is Vimeo video
            $videoId = explode("vimeo.com/",$url)[1];
            if(strpos($videoId, '&') !== false){
                $videoId = explode("&",$videoId)[0];
            }
            $finalUrl.='https://player.vimeo.com/video/'.$videoId;
        }else if(strpos($url, 'youtube.com/') !== false) {
            //it is Youtube video
            $videoId = explode("v=",$url)[1];
            if(strpos($videoId, '&') !== false){
                $videoId = explode("&",$videoId)[0];
            }
            $finalUrl.='https://www.youtube.com/embed/'.$videoId;
        }else if(strpos($url, 'youtu.be/') !== false){
            //it is Youtube video
            $videoId = explode("youtu.be/",$url)[1];
            if(strpos($videoId, '&') !== false){
                $videoId = explode("&",$videoId)[0];
            }
            $finalUrl.='https://www.youtube.com/embed/'.$videoId;
        }else{
            //Enter valid video URL
        }
        return $finalUrl;
    }

    public function getFullJob ($id, $include = []){
        $job = null;

        if (is_array($include) && !empty($include)) {
            $job = JobPr::where('id', $id)->with($include)->first();
        } else {
            $with_relations = [
                'jobs',
                'companyPr.company',
                'workinglanguages',
                'desiredlanguages',
                'shifts',
                'files',
                'videos'
            ];

            $job = JobPr::where('id', $id)->with($with_relations)->first();
        }

        return $job;
    }

    public function updateJob(UpdateJobRequest $request){
        $response = null;
        $data['job_pr'] = $request->only([
            'company_pr_id',
            "status",
            "deadline",
            "required_candidates",
            "creator",
            "contact_name",
            "contact_email",
            "contact_number",
            "training",
            "observation",
            "desired_language_comment",
            "salary",
            "salary_type",
            "salary_amount_1",
            "salary_amount_2",
            "job_type",
            "duration_type",
            "employment_type",
            "job_type_comment",
            "transport",
            "transport_comment",
            "working_hours",
            "working_hours_comment",
            "clothes",
            "clothes_comment",
            'shifts'
        ]);

        $data['job'] = $request->only([
            'offer_name',
            'title',
            "benefits",
            "location",
            "department",
            'description',
            "requirements",
            "comments",
            "additional_information",
            'recess'
        ]);

        DB::transaction(function () use ($request, $data, &$response){
            JobPr::disableSearchSyncing();
            $timeline_data = [Timeline::OLD_VALUES => null, Timeline::NEW_VALUES => null];
            $timeline_relations_data = [
                'company_pr_id' => ['relation' => Company::class, 'identity_column' => 'company_pr_id', 'record_column_name' => 'name', 'with' => 'company']
            ];
            $job_pr_object = JobPr::find($request->job_pr_id);
            if(is_null($job_pr_object)){
                throw new ModelNotFoundException('Record Not Found');
            }
            $job_pr_object->fill($data['job_pr']);
            if($job_pr_object->isDirty()){
                $this->recordChanges($job_pr_object, $timeline_data, $timeline_relations_data);
            }
            $job_pr_object->save();


            $job_object = Job::find($request->job_id);
            $job_object->fill($data['job']);
            if($job_object->isDirty()){
                $this->recordChanges($job_object, $timeline_data);
            }
            $job_object->save();

            $this->recordSyncChanges($job_pr_object->workinglanguages()->sync($request->get('work_language')), $job_pr_object, 'workinglanguages', 'name', $timeline_data);

            $desired_languages = $request->get('desired_language');
            if(!is_null($desired_languages) && is_array($desired_languages) && !empty($desired_languages)){
                $old_desired_languages = $job_pr_object->desiredlanguages;

                //Delete Desired Language
                $delete_desired_languages = $old_desired_languages->whereNotIn('id', $desired_languages);
                if($delete_desired_languages->isNotEmpty()){
                    foreach ($delete_desired_languages as $delete_language){
                        $job_pr_object->desiredlanguages()->detach($delete_language);
                        $this->recordOneToManyDelete($delete_language, 'desiredlanguages', 'name', $timeline_data);
                    }
                }

                foreach ($desired_languages as $language){
                    if(!is_null($language)){
                        //Add new desired language
                        if(is_null($old_desired_languages->firstWhere('id', $language))){
                            $job_pr_object->desiredlanguages()->attach($language);
                            $this->recordOneToManyNew('desiredlanguages', Language::find($language)->name, $timeline_data);
                        }
                    }
                }
            }

            $shift_data = (is_null($request->get('shifts_data'))) ? collect([]) : collect($request->get('shifts_data'));
            if ($shift_data->isNotEmpty()){
                $old_shifts = $job_pr_object->shifts()->get();
                $delete_shifts = $old_shifts->whereNotIn('id', $shift_data->whereNotNull('id')->pluck('id')->all());
                if($delete_shifts->isNotEmpty()){
                    foreach ($delete_shifts as $single_shift){
                        $this->recordOneToManyDelete($single_shift, 'shiftsR', 'start_time', $timeline_data);
                        $single_shift->delete();
                    }
                }

                $new_shifts = $shift_data->whereNull('id');
                if($new_shifts->isNotEmpty()){
                    foreach ($new_shifts as $single_shift){
                        unset($single_shift['id']);
                        $job_pr_object->shifts()->create($single_shift);
                        $this->recordOneToManyNew('shiftsR', $single_shift['start_time'], $timeline_data);
                    }
                }

                $update_shifts = $shift_data->whereNotNull('id');
                if($update_shifts->isNotEmpty()){
                    foreach ($update_shifts as $single_shift){
                        $temp_update_shift = JobsShift::find($single_shift['id']);
                        unset($single_shift['id']);
                        $temp_update_shift->fill($single_shift);
                        if($temp_update_shift->isDirty()){
                            $this->recordOneToManyUpdate($temp_update_shift, 'shiftsR', $timeline_data);
                        }
                        $temp_update_shift->save();
                    }
                }
            }

            $posted_old_files = (is_null($request->get('old_files'))) ? collect([]) : collect($request->get('old_files'));
            if ($posted_old_files->isNotEmpty()){
                $old_files = $job_pr_object->files;
                //if user submit old_files[0][id] with empty value, then it will delete all records
                $delete_files = $old_files->whereNotIn('id', $posted_old_files->whereNotNull('id')->pluck('id')->all());
                if($delete_files->isNotEmpty()){
                    foreach ($delete_files as $single_file){
                        $this->recordOneToManyDelete($single_file, 'jobFiles', 'file_name', $timeline_data);
                        $single_file->delete();
                    }
                }
            }

            //Upload New File
            if ($request->hasFile('files')) {
                $allowedfileExtension = ['docx', 'xls', 'csv', 'zip', 'pdf', 'PDF', 'jpg', 'JPG', 'jpeg', 'png'];
                $files = $request->file('files');

                foreach ($files as $file) {
                    $extension = strtolower($file->getClientOriginalExtension());
                    $check = in_array($extension, $allowedfileExtension);

                    if ($check) {
                        $data['path'] = $file->store(self::JOB_FILES_UPLOAD_PATH, self::JOB_FILES_UPLOAD_DISK);
                        $data['file_name'] = $file->getClientOriginalName();
                        $job_pr_object->files()->create($data);
                        $this->recordOneToManyNew('jobFiles', $data['file_name'], $timeline_data);
                    } else {
                        throw new Exception(self::JOB_FILES_INVALID_FORMAT, Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
                }
            }


            $posted_old_videos = (is_null($request->get('old_video'))) ? collect([]) : collect($request->get('old_video'));
            if ($posted_old_videos->isNotEmpty()){
                $old_videos = $job_pr_object->videos;
                //if user submit old_video[0][id] with empty value, then it will delete all records
                $delete_videos = $old_videos->whereNotIn('id', $posted_old_videos->whereNotNull('id')->pluck('id')->all());
                if($delete_videos->isNotEmpty()){
                    foreach ($delete_videos as $single_video){
                        //I'm doing here because of tracking two separate fields
                        $timeline_data['new_values']['jobVideos'][] = null;
                        $timeline_data['old_values']['jobVideos'] [] = (is_null($single_video->link)) ? $single_video->file_name : $single_video->link;
                        $single_video->delete();
                    }
                }
            }

            //Upload New Videos
            if ($request->has('video')) {
                $videos = $request->get('video');
                $video_files = $request->file('video');
                if(is_array($videos) && !empty($videos)){
                    foreach ($videos as $key => $video){
                        if($video['type']  == (int)2){
                            $job_pr_object->videos()->create([
                                'type' => JobsVideo::VIDEO_TYPE_LINK,
                                'link' => $video['link']
                            ]);
                            $this->recordOneToManyNew('jobVideos', $video['link'], $timeline_data);
                        }else{
                            $allowedfileExtension = ['mov', 'mp4', '3gp', 'MP4', 'MOV', 'AVI', 'MKV', 'avi', 'mkv'];
                            if(isset($video_files[$key]['file'])){
                                $extension = strtolower($video_files[$key]['file']->getClientOriginalExtension());
                                $check = in_array($extension, $allowedfileExtension);
                                if ($check) {
                                    $data['path'] = $video_files[$key]['file']->store(self::JOB_VIDEOS_UPLOAD_PATH, self::JOB_FILES_UPLOAD_DISK);
                                    $data['file_name'] = $video_files[$key]['file']->getClientOriginalName();
                                    $job_pr_object->videos()->create($data);
                                    $this->recordOneToManyNew('jobVideos', $data['file_name'], $timeline_data);
                                } else {
                                    throw new Exception(self::JOB_VIDEOS_INVALID_FORMAT, Response::HTTP_UNPROCESSABLE_ENTITY);
                                }
                            }

                        }
                    }
                }
            }

            $job_pr_object->searchable();
            JobUpdated::dispatch($job_pr_object, 'Job Updated', $timeline_data['old_values'], $timeline_data['new_values']);

            $response = true;
        });

        JobPr::enableSearchSyncing();
        return $response;
    }

    public function deleteJob($job_pr_id){
        $job_pr = JobPr::where('id', $job_pr_id)->first();
        if(is_null($job_pr)){
            throw new ModelNotFoundException('Record Not Found');
        }
        $response = null;
        DB::transaction(function () use ($job_pr, &$response){
            JobPr::disableSearchSyncing();
            $job_pr->candidates()->detach();
            $job_pr->videos()->delete();
            $job_pr->files()->delete();
            $job_pr->shifts()->delete();
            $job_pr->desiredlanguages()->detach();
            $job_pr->workinglanguages()->detach();
            $job_pr->jobs()->delete();
            $job_pr->unsearchable();
            $response = $job_pr->delete();
        });

        return $response;
    }

    public function deleteJobVideo($job_video_id){
        $job_video_object = JobsVideo::find($job_video_id);
        if(is_null($job_video_object)){
            throw new ModelNotFoundException('Record Not Found');
        }
        $response = false;
        DB::transaction(function () use ($job_video_object, &$response){
            $job_pr = $job_video_object->jobPr;
            $name = (is_null($job_video_object->file_name) && !is_null($job_video_object->link)) ? 'Youtube Link' : $job_video_object->file_name;
            $job_video_object->delete();
            DeleteJobVideo::dispatch($job_pr, 'Delete Job Video', $name);

            $response = true;
        });

        return $response;
    }

    public function getCandidates($job_pr_id){
        $job_pr_object = JobPr::find($job_pr_id);
        if(is_null($job_pr_object)){
            throw new ModelNotFoundException('Record Not Found');
        }

        return $job_pr_object->candidates()->with('action')->get();
    }

    public function store ($data){
        $job_model = new Job();
        $job_model->fill($data);
        return $job_model->saveQuietly();
    }

    public function updateStatus($job_pr_id, $token, $status = JobPr::STATUS_ACTIVE, $request)
    {
        try {
            $response = null;
            DB::transaction(function () use ($job_pr_id, $status, $token, $request, &$response) {
                $job_pr = JobPr::findOrFail($job_pr_id);
                
                $job_pr->status = $status;
                $response = $job_pr->save();

                if ($request->hasFile('files')) {
                    $allowedfileExtension = ['docx', 'xls', 'csv', 'zip', 'pdf', 'PDF', 'jpg', 'JPG', 'jpeg', 'png'];
                    $files = $request->file('files');
    
                    foreach ($files as $file) {
                        if(!is_null($file)){
                            $extension = strtolower($file->getClientOriginalExtension());
                            $check = in_array($extension, $allowedfileExtension);
        
                            if ($check) {
                                $data['path'] = $file->store(self::JOB_FILES_UPLOAD_PATH, self::JOB_FILES_UPLOAD_DISK);
                                $data['file_name'] = $file->getClientOriginalName();
                                $job_pr->files()->create($data);
                            } else {
                                throw new Exception(self::JOB_FILES_INVALID_FORMAT, Response::HTTP_UNPROCESSABLE_ENTITY);
                            }
                        }
                    }
                }

                if (!is_null($response) && $response) {
                    $auto_generated_url = $job_pr->autoGenerateURL()->where('token', $token)->first();
                    if (is_null($auto_generated_url)) {
                        throw new ModelNotFoundException('URL record not found');
                    }

                    InvalidateUrl::dispatch($auto_generated_url);
                    $link_sender = UserPr::where('id', $auto_generated_url->user_pr_id)->first();
                    if (is_null($link_sender)) {
                        throw new ModelNotFoundException('Recruiter who send link is not Found');
                    }

                    CompanyConfirmedJob::dispatch($job_pr, 'Job Confirmed by Company');
                    

                    Notification::send($link_sender, new NotificationsCompanyConfirmedJob([
                        'title' => "Ettevõte ". $job_pr->companyPr->company[0]->name ." on kinnitanud tööpakkumise",
                        'image' => null
                    ]));
                }
            });

            return $response;

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getActiveJobs($include = []){
        try {
            if (is_array($include) && !empty($include)) {
                $active_jobs = JobPr::where('status', JobPr::STATUS_ACTIVE)->with($include)->get();
            } else {
                $with_relations = [
                    'jobs',
                    'companyPr.company',
                    'workinglanguages',
                    'desiredlanguages',
                    'shifts',
                    'files',
                    'videos'
                ];
    
                $active_jobs = JobPr::where('status', JobPr::STATUS_ACTIVE)->with($with_relations)->get();
            }

            return $active_jobs;
    
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
