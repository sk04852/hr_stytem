<?php
namespace App\Http\Controllers\Timelines\Models;

use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\Jobs\Models\JobPr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timeline extends Model
{
    use SoftDeletes;

    const OLD_VALUES = 'old_values';
    const NEW_VALUES = 'new_values';


	protected $table = 'timelines';

	public $timestamps = true;

	public static $snakeAttributes = false;

    protected $fillable = [
        'id',
        'model_type',
        'model_id',
        'user_pr_id',
        'title',
        'action',
        'old_values',
        'new_values',
        'data',
        'additional_information'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['detail_description'];

    protected $hidden = [
//        'created_at',
        'updated_at',
    ];

    /**
     * Below are the relationships which are manged on Parent Page
     * @var string[]
     */
    protected $model_parent_many_to_many_relationships = [
        'nationalities',
        'jobTypes',
        'driving_licenses',
        'recommendations',
        'companyContacts',
        'companylocations',
        'companyFiles',
        'desiredlanguages',
        'shiftsR',
        'jobFiles',
        'jobVideos',
        'industries',
        'languages',
        'tags'
    ];


    /**
     * Below are the relationships which are manged on Separate Page
     * @var string[]
     */
    protected $model_separate_to_many_relationships = [
        'jobHistory',
        'education',
        'additionalCourses',
        'languages',
        'files',
        'agreement'
    ];


    protected array $model_boolean_values = [
        'consent',
        'newsletter',
        'still_working'
    ];

    public function getDetailDescriptionAttribute()
    {
        $response = null;
        if(is_null($this->old_values) && is_null($this->new_values)){
            return $response;
        }

        $old_values = collect(json_decode($this->old_values));
        $new_values = collect(json_decode($this->new_values));

        if($old_values->isNotEmpty() && $new_values->isNotEmpty()){
            // App::setLocale('et');
            $response = '<ul>';
            foreach ($old_values as $key => $value){
                /**
                 * Parent Page
                 * Start Handling Many-to-Many Relationships
                 */
                if(in_array($key, $this->model_parent_many_to_many_relationships)){
                    if(!empty($old_values[$key])){
                        foreach ($old_values[$key] as $index => $row){
                            //Added new Record
                            if(is_null($row)){
                                $response .= '<li>'. __("db_column_name.$key") . ' <span class="font-italic font-weight-bold">' . Str::limit($new_values[$key][$index], 50) .'</span> Added</li>';
                            }


                            //Deleted Record
                            if(is_null($new_values[$key][$index])){
                                $response .= '<li>'. __("db_column_name.$key") . ' <span class="font-italic font-weight-bold">' . Str::limit($old_values[$key][$index], 50) .'</span> Deleted</li>';
                            }

                            //Update Records
                            if(!is_null($new_values[$key][$index]) && !is_null($row)){
                                $response .= '<li>'. __("db_column_name.$key") .'<ul>';
                                foreach ($row as $inner_index => $inner_row){
                                    $response .= '<li>'. __("db_column_name.$inner_index")  .' is change from <span class="font-italic font-weight-bold">'. Str::limit($inner_row, 50) .'</span> to <span class="font-italic font-weight-bold">'. Str::limit($new_values[$key][$index]->$inner_index, 50) .'</span></li>';
                                }
                                $response .= '</ul></li>';
                            }
                        }
                    }
                    continue;
                }
                /**
                 * Parent Page
                 * End Handling Many-to-Many Relationships
                 */


                /**
                 * Separate Page
                 * Start Handling Many-to-Many Relationships
                 */
                if(in_array($key, $this->model_separate_to_many_relationships)){
                    //Added new Record
                    if(is_null($old_values[$key][0])){
                        $response .= '<li>'. __("db_column_name.$key") . ' <span class="font-italic font-weight-bold">' . Str::limit($new_values[$key][0], 50) .'</span> Added</li>';
                    }

                    //Deleted Record
                    if(is_null($new_values[$key][0])){
                        $response .= '<li>'. __("db_column_name.$key") . ' <span class="font-italic font-weight-bold">' . Str::limit($old_values[$key][0], 50) .'</span> Deleted</li>';
                    }


                    //Update Records
                    if(!is_null($new_values[$key][0]) && !is_null($old_values[$key][0])){
                        $response .= '<li>'. __("db_column_name.$key") .'<ul>';
                        foreach ($old_values[$key] as $index => $row){
                            foreach ($row as $inner_index => $inner_row){
                                $response .= '<li>'. __("db_column_name.$inner_index")  .' is change from <span class="font-italic font-weight-bold">'. Str::limit($inner_row, 50) .'</span> to <span class="font-italic font-weight-bold">'. Str::limit($new_values[$key][$index]->$inner_index, 50) .'</span></li>';
                            }
                        }
                        $response .= '</ul></li>';
                    }
                    continue;
                }
                /**
                 * Separate Page
                 * End Handling Many-to-Many Relationships
                 */


                /**
                 * Start handling Candidate Gender
                 */
                if($key == 'gender'){
                    if($new_values[$key] == CandidateCV::GENDER_MALE){
                        $temp_new = 'Mees';
                    }elseif($new_values[$key] == CandidateCV::GENDER_FEMALE){
                        $temp_new = 'Naine';
                    }

                    if($value == CandidateCV::GENDER_MALE){
                        $temp_old = 'Mees';
                    }elseif($value == CandidateCV::GENDER_FEMALE){
                        $temp_old = 'Naine';
                    }

                    $response .= '<li>'. __("db_column_name.$key")  .' marked from <span class="font-italic font-weight-bold">'. $temp_old .'</span> to <span class="font-italic font-weight-bold">'. $temp_new .'</span></li>';
                    continue;
                }
                /**
                 * End handling Candidate Gender
                 */


                /**
                 * Start handling Job Status boolean value
                 */
                if($key == 'status'){
                    if($new_values[$key] == JobPr::STATUS_ACTIVE){
                        $temp_new = 'Aktiivne';
                    }elseif($new_values[$key] == JobPr::STATUS_INACTIVE){
                        $temp_new = 'Mitteaktiivne';
                    }elseif($new_values[$key] == JobPr::STATUS_HOLD){
                        $temp_new = 'Ootel';
                    }

                    if($value == JobPr::STATUS_ACTIVE){
                        $temp_old = 'Aktiivne';
                    }elseif($value == JobPr::STATUS_INACTIVE){
                        $temp_old = 'Mitteaktiivne';
                    }elseif($value == JobPr::STATUS_HOLD){
                        $temp_old = 'Ootel';
                    }

                    $response .= '<li>'. __("db_column_name.$key")  .' marked from <span class="font-italic font-weight-bold">'. $temp_old .'</span> to <span class="font-italic font-weight-bold">'. $temp_new .'</span></li>';
                    continue;
                }
                /**
                 * End handling Job Status boolean value
                 */

                /**
                 * Start handling Candidate boolean value
                 */
                if(in_array($key, $this->model_boolean_values)){
                    $temp_new = ($new_values[$key]) ? 'Checked' : 'Unchecked';
                    $temp_old = ($value) ? 'Checked' : 'Unchecked';
                    $response .= '<li>'. __("db_column_name.$key")  .' marked from <span class="font-italic font-weight-bold">'. $temp_old .'</span> to <span class="font-italic font-weight-bold">'. $temp_new .'</span></li>';
                    continue;
                }
                /**
                 * End handling Candidate boolean value
                 */


                /**
                 * Start Handling Photo
                 */
                if($key == 'photo'){
                    $temp_msg = (is_null($value)) ? ' add new picture' : ' is updated';
                    $response .= '<li>'. __("db_column_name.$key") ." $temp_msg</li>";
                    continue;
                }
                /**
                 * end Handling Photo
                 */


                if(is_null($value)){
                    $response .= '<li>'. __("db_column_name.$key")  .' is Added</li>';
                }else {
                    $response .= '<li>'. __("db_column_name.$key")  .' is change from <span class="font-italic font-weight-bold">'. Str::limit($value, 50) .'</span> to <span class="font-italic font-weight-bold">'. Str::limit($new_values[$key], 50) .'</span></li>';
                }
            }
            $response .= '</ul>';
            // App::setLocale('en');
        }

        return $response;
    }

    /**
     * Get the parent url model (post or video).
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Get the User Pr record associated with the language.
     */
    public function userPr()
    {
        return $this->belongsTo('App\Models\UserPr', 'user_pr_id', 'id');
    }

    /**
     * Get the Comments record associated with the timeline.
     */
    public function comments()
    {
        return $this->hasMany('App\Http\Controllers\Timelines\Models\TimelineComment', 'timeline_id', 'id');
    }
}
