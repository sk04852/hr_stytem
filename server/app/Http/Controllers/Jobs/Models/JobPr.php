<?php

namespace App\Http\Controllers\Jobs\Models;

use App\Models\JobApplicants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\Actions\Models\Action;
use ElasticScoutDriverPlus\Searchable;

class JobPr extends Model
{
    use SoftDeletes;
    use \Altek\Eventually\Eventually;
    use \Bkwld\Cloner\Cloneable;
    use Searchable;

    protected $cloneable_relations = [
        'jobs',
        'workinglanguages',
        'desiredlanguages',
        'shifts',
        'files',
        'videos',
        'shifts'
    ];

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 2;
    public const STATUS_HOLD = 3;

    public const JOB_DURATION_PERMANENT = 1;
    public const JOB_DURATION_TEMPORARY = 2;

    public const EMPLOYMENT_TYPE_FULL_TIME = 1;
    public const EMPLOYMENT_TYPE_PART_TIME = 2;
    public const EMPLOYMENT_TYPE_SUBSTITUE_TIME = 3;

    protected $table = 'jobs_pr';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $appends = ['duration_type_name', 'employment_type_name'];

    protected $fillable = [
        'company_pr_id',
        "status",
        "deadline",
        'desired_start_time',
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
        'shifts',
        'auto_generate_url_id'
    ];

    /**
     * Recordable events.
     *
     * @var array
     */
    protected $recordableEvents = [
        //        'retrieved',
        'created',
        'updated',
        //        'restored',
        //        'deleted',
        //        'forceDeleted',
        'existingPivotUpdated',
        'attached',
        'detached',
    ];

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'jobs';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->only([
            "status",
            "deadline",
            'desired_start_time',
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
            "duration_type_name",
            "employment_type",
            "employment_type_name",
            "job_type_comment",
            "transport",
            "transport_comment",
            "working_hours",
            "working_hours_comment",
            "clothes",
            "clothes_comment",
            'shifts',
        ]);

        $array['id'] = $this->id;
        $job_object = $this->jobs->first();
        $array['company_name'] = (is_null($this->company_pr_id)) ? null : $this->companyPr->Company()->first()->name;
        if(!is_null($job_object)){
            $array['offer_name'] = $job_object->offer_name;
            $array['benefits'] = $job_object->benefits;
            $array['location'] = $job_object->location;
            $array['department'] = $job_object->department;
            $array['description'] = $job_object->description;
            $array['requirements'] = $job_object->requirements;
            $array['comments'] = $job_object->comments;
            $array['additional_information'] = $job_object->additional_information;
            $array['recess'] = $job_object->recess;
        }

        $array['working_languages'] = $this->workinglanguages->map(function ($data) {
            return $data->name;
        });

        $array['desired_languages'] = $this->desiredlanguages->map(function ($data) {
            return $data->name;
        });

        $job_shifts = $this->shifts()->get();
        $array['shifts_data'] = $job_shifts->map(function ($data) {
            return [
                'start_time' => $data->start_time,
                'end_time' => $data->end_time
            ];
        });

        return $array;
    }

    public function jobs()
    {
        return $this->hasMany('App\Http\Controllers\Jobs\Models\Job', 'job_pr_id', 'id');
    }

    public function companyPr()
    {
        return $this->belongsTo('App\Http\Controllers\Companies\Models\CompanyPr', 'company_pr_id', 'id');
    }

    public function workinglanguages()
    {
        return $this->belongsToMany('App\Http\Controllers\Languages\Models\Language', 'jobs_work_language', 'job_pr_id', 'language_id');
    }

    public function desiredlanguages()
    {
        return $this->belongsToMany('App\Http\Controllers\Languages\Models\Language', 'jobs_desired_language', 'job_pr_id', 'language_id');
    }

    public function shifts()
    {
        return $this->hasMany('App\Http\Controllers\Jobs\Models\JobsShift', 'job_pr_id', 'id');
    }

    public function files()
    {
        return $this->hasMany('App\Http\Controllers\Jobs\Models\JobsFile', 'job_pr_id', 'id');
    }

    public function videos()
    {
        return $this->hasMany('App\Http\Controllers\Jobs\Models\JobsVideo', 'job_pr_id', 'id');
    }

    public function candidates()
    {
        return $this->belongsToMany('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_jobs', 'job_pr_id', 'candidatecv_id')->withPivot('action_id', 'user_pr_id')->withTimestamps();
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'candidatecv_jobs', 'job_pr_id', 'action_id')->withPivot('candidatecv_id', 'user_pr_id')->withTimestamps();
    }

    public function timeline()
    {
        return $this->morphMany('App\Http\Controllers\Timelines\Models\Timeline', 'model');
    }

    //    public function jobsFilter($request)
    //    {
    //
    //        return Job::when(!empty($request->JobID), function ($query) use ($request) {
    //            return $query->where('Job-ID', '=', $request->JobID);
    //        })
    //            ->when(!empty($request->status), function ($query) use ($request) {
    //                $query->where('Status', 'like', '%' . $request->status . '%');
    //            });
    //    }

    //updated function
    //    public function JobApplicats()
    //    {
    //        return $this->hasMany(JobApplicants::class, 'job_ID')->where('action_name', 'General Candidates List') ;
    //    }

    /**
     * Get all auto generate urls.
     */
    public function autoGenerateURL()
    {
        return $this->morphMany('App\Models\AutoGenerateURL', 'model');
    }

    public function getDurationTypeNameAttribute(){
        if(!is_null($this->duration_type)){
            if($this->duration_type == self::JOB_DURATION_PERMANENT){
                return 'püsiv töökoht';
            }
            if($this->duration_type == self::JOB_DURATION_TEMPORARY){
                return 'ajutine töökoht';
            }
        }
    }
    public function getEmploymentTypeNameAttribute(){
        if(!is_null($this->duration_type)){
            if($this->duration_type == self::EMPLOYMENT_TYPE_FULL_TIME){
                return 'täistööaeg';
            }
            if($this->duration_type == self::EMPLOYMENT_TYPE_PART_TIME){
                return 'osaline tööaeg';
            }
            if($this->duration_type == self::EMPLOYMENT_TYPE_SUBSTITUE_TIME){
                return 'asendustöö';
            }
        }
    }
}
