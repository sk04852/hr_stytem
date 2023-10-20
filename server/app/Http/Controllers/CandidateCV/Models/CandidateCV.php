<?php

namespace App\Http\Controllers\CandidateCV\Models;

use App\Http\Controllers\Actions\Models\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Laravel\Scout\Searchable;
use ElasticScoutDriverPlus\Searchable;

class CandidateCV extends Model
{
    use SoftDeletes, Searchable;


    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    protected $table = 'candidatecv';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'phone',
        'email',
        'location',
        'job_type',
        'language_id',
        'mother_language',
        'description',
        'source',
        'photo',
        'consent',
        'newsletter',
        'user_id',
        'personal_information',
        'personal_code',
        'children_qty',
        'children_names',
        'marital_status',
        'desired_job',
        'desired_salary',
        'desired_job_time',
        'desired_job_location',
        'age',
        'keywords',
        'status',
        'timeline_action_type_id'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name', 'gender_name'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'candidates';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->only([
            'first_name',
            'last_name',
            'gender',
            'gender_name',
            'dob',
            'phone',
            'email',
            'location',
            'job_type',
            'mother_language',
            'description',
            'source',
            'personal_information',
            'personal_code',
            'children_qty',
            'children_names',
            'marital_status',
            'desired_job',
            'desired_salary',
            'desired_job_time',
            'desired_job_location',
            'age',
            'keywords',
            'status'
        ]);

        $array['id'] = $this->id;
        $array['language'] = (is_null($this->language)) ? null : $this->language->name;
        $array['action'] = (is_null($this->action)) ? null : $this->action->name;

        $array['nationalities'] = $this->nationalities->map(function ($data) {
            return $data->name;
        });

        $array['driving_licenses'] = $this->driving_licenses->map(function ($data) {
            return [
                'level' => $data->level,
                'issue_data' => $data->issue_date,
                'expiry_data' => $data->expiry_date,
            ];
        });

        $array['recommendations'] = $this->recommendations->map(function ($data) {
            return $data->recommendation;
        });

        $array['tags'] = $this->tags->map(function ($data) {
            return $data->name;
        });

        $array['job_histories'] = $this->jobHistory->map(function ($data) {
            return [
                'company_name' => $data->company_name,
                'designation' => $data->designation,
                'starting_year' => $data->starting_year,
                'starting_month' => $data->starting_month,
                'starting_day' => $data->starting_day,
                'ending_year' => $data->ending_year,
                'ending_month' => $data->ending_month,
                'ending_day' => $data->ending_day,
                'still_working' => $data->still_working,
                'description' => $data->description,
                'work_place' => $data->work_place
            ];
        });

        $array['education'] = $this->education->map(function ($data) {
            return [
                'degree' => (is_null($data->education_degree)) ? null : $data->education_degree->name,
                'level' => (is_null($data->education_level)) ? null : $data->education_level->name,
                'institute' => $data->institute,
                'starting_year' => $data->starting_year,
                'starting_month' => $data->starting_month,
                'starting_day' => $data->starting_day,
                'ending_year' => $data->ending_year,
                'ending_month' => $data->ending_month,
                'ending_day' => $data->ending_day,
                'speciality' => $data->speciality,
                'still_studying' => $data->still_studying,
                'additonal_information' => $data->additonal_information,
            ];
        });

        $array['additional_courses'] = $this->additionalCourses->map(function ($data) {
            return [
                'title' => $data->title,
                'description' => $data->description,
                'starting_year' => $data->starting_year,
                'starting_month' => $data->starting_month,
                'starting_day' => $data->starting_day,
                'ending_year' => $data->ending_year,
                'ending_month' => $data->ending_month,
                'ending_day' => $data->ending_day,
                'total_hours' => $data->total_hours
            ];
        });

        $array['skills'] = $this->skills->map(function ($data) {
            return $data->name;
        });

        $array['languages'] = $this->languages->map(function ($data) {
            return $data->name;
        });

        return $array;
    }


    /**
     * Get the Nationality record associated with the Candidate.
     */
    public function nationalities()
    {
        return $this->belongsToMany('App\Http\Controllers\Nationalities\Models\Nationality', 'candidatecv_nationality', 'candidatecv_id', 'nationality_id');
    }

    /**
     * Get the Driving Licenses record associated with the Candidate.
     */
    public function driving_licenses()
    {
        return $this->hasMany('App\Http\Controllers\CandidateCV\Models\CandidateCVDrivingLicense', 'candidatecv_id', 'id');
    }

    /**
     * Get the Recommendations record associated with the Candidate.
     */
    public function recommendations()
    {
        return $this->hasMany('App\Http\Controllers\CandidateCV\Models\CandidateCVRecommender', 'candidatecv_id', 'id');
    }

    /**
     * Get the Language record associated with the language.
     */
    public function language()
    {
        return $this->belongsTo('App\Http\Controllers\Languages\Models\Language');
    }

    /**
     * Get the tags record associated with the candidate.
     */
    public function tags()
    {
        return $this->belongsToMany('App\Http\Controllers\Tags\Models\Tag', 'candidatecv_tag', 'candidatecv_id', 'tag_id');
    }

    /**
     * Get Job History for the CandidateCv.
     */
    public function jobHistory()
    {
        return $this->hasMany('App\Http\Controllers\CandidateCV\Models\CandidateCVJobHistory', 'candidatecv_id', 'id');
    }

    /**
     * Get Education for the CandidateCv.
     */
    public function education()
    {
        return $this->hasMany('App\Http\Controllers\CandidateCV\Models\CandidateCVEducation', 'candidatecv_id', 'id');
    }

    /**
     * Get Additional Courses for the CandidateCv.
     */
    public function additionalCourses()
    {
        return $this->hasMany('App\Http\Controllers\CandidateCV\Models\CandidateCVAdditionalCourse', 'candidatecv_id', 'id');
    }

    /**
     * Get the skills record associated with the candidate.
     */
    public function skills()
    {
        return $this->belongsToMany('App\Http\Controllers\Skills\Models\Skill', 'candidatecv_skill', 'candidatecv_id', 'skill_id')->withTimestamps();;
    }

    /**
     * Get the languages record associated with the candidate.
     */
    public function languages()
    {
        return $this->belongsToMany('App\Http\Controllers\Languages\Models\Language', 'candidatecv_language', 'candidatecv_id', 'language_id')->withPivot('level', 'speaking', 'reading_writing')->withTimestamps();;
    }

    /**
     * Get files for the CandidateCv.
     */
    public function files()
    {
        return $this->hasMany('App\Http\Controllers\CandidateCV\Models\CandidateCVFiles', 'candidatecv_id', 'id');
    }

    /**
     * Get files for the CandidateCv.
     */
    public function agreement()
    {
        return $this->hasOne('App\Http\Controllers\CandidateCV\Models\CandidateCVAgreement', 'candidatecv_id', 'id');
    }

    /**
     * Get all auto generate urls.
     */
    public function autoGenerateURL()
    {
        return $this->morphMany('App\Models\AutoGenerateURL', 'model');
    }

    /**
     * Get Recruiter for the CandidateCv.
     */
    public function recruiter()
    {
        return $this->belongsTo('App\Models\UserPr', 'user_id', 'id');
    }

    public function custom_lists()
    {
        return $this->belongsToMany('App\Http\Controllers\CustomCandidate\Models\CustomCadidateListName', 'custom_candidate_lists', 'candidatecv_id', 'list_name_id');
    }

    /**
     * Get the CandidateCV record associated with the Job History.
     */
    public function jobTypes()
    {
        return $this->belongsToMany('App\Http\Controllers\Jobs\Models\JobTypes', 'candidate_job_types', 'candidatecv_id', 'job_type_id');
    }

    public function jobs()
    {
        return $this->belongsToMany('App\Http\Controllers\Jobs\Models\JobPr', 'candidatecv_jobs', 'candidatecv_id', 'job_pr_id')->withPivot('action_id', 'user_pr_id')->withTimestamps();
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'candidatecv_jobs', 'candidatecv_id', 'action_id');
    }

    public function getFullNameAttribute($value)
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getGenderNameAttribute($value)
    {
        if($this->gender == 1){
            return 'Mees';
        }

        if($this->gender == 2){
            return 'Naine';
        }

        return null;
    }

    public function getPhotoAttribute($value)
    {
        return asset('storage/' . $value);
    }

    public function candidateCVFilter($request)
    {

        return CandidateCV::when(!empty($request->cv_id), function ($query) use ($request) {
            return $query->where('CV-ID', '=', $request->cv_id);
        })
            ->when(!empty($request->candidate->ID), function ($query) use ($request) {
                $query->where('candidate-ID', $request->candidate->ID);
            });
    }

    public function jobAppliedBy()
    {
        return $this->belongsToMany('App\Models\UserPr', 'candidatecv_jobs', 'candidatecv_id', 'user_pr_id')->withPivot('job_pr_id', 'action_id')->withTimestamps();
    }

    public function timeline()
    {
        return $this->morphMany('App\Http\Controllers\Timelines\Models\Timeline', 'model');
    }

    public function timelineActionType()
    {
        return $this->belongsTo('App\Http\Controllers\Timelines\Models\TimelineActionType', 'timeline_action_type_id', 'id');
    }

    public function agrelloResponses()
    {
        return $this->hasMany('App\Http\Controllers\Agrello\Models\AgrelloResponse', 'candidatecv_id', 'id');
    }
}
