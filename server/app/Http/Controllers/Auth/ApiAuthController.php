<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Actions\Models\Action;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\Concerns\Has;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Auth\Requests\UpdateProfileRequest;
use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\Timelines\Models\Timeline;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class ApiAuthController extends Controller
{

    const USER_PHOTO_UPLOAD_PATH = 'uploads/users/photos';
    const USER_PHOTO_UPLOAD_DISK = 'candidatecv';
    const USER_PHOTO_INVALID_FORMAT = 'Candidate invalid upload files. only (jpg, jpeg, png, bmp, gif, svg, or webp) allowed';
    const USER_PHOTO_UPLOAD_ERROR = 'Photo not found';

    public function __construct(UserPr $model)
    {
        parent::__construct($model);
    }

    public function register(Request $request)
    {
        // return $request;
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = UserPr::create([
            'userpr_ID' => 1,
            'user_ID' => 1,
            'photo' => 'avatar.jpg',
            'int_code' => 1,
            'phone' => '03008483008',
            'email' => $request['email'],
            'password' => $request['password'],
            'timezone' => 'pkr',
            'permission_ID' => 1
        ]);

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];
        return response($response, 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = UserPr::where('email', $request->email)->first();

        if ($user) {
            if (Hash::check($request['password'], $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;

                $permissions = [];
                $roles = $user->roles;
                if($roles->isNotEmpty()){
                    foreach ($roles as $role){
                        $permissions = $role->permissions()->pluck('name')->toArray();
                    }
                }

                $response['token'] = $token;
                $response['user'] = $user;
                $response['user']['permissions'] = $permissions;
                return response($response, 200);
            } else {
                $response = ["message" => "Email or Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" => 'User does not exist'];
            return response($response, 422);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if (is_null($user)) {
            return response(["message" => "Invalid token"], 422);
        }
        $token = $user->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function profile(Request $request)
    {
        // $startDate = date_format(Carbon::today(), 'Y-m-d');
        $startDate = date('Y-m-d 00:00:00', strtotime(Carbon::today()));
        $endDate = date('Y-m-d 00:00:00', strtotime(Carbon::today()->subDay(7)));

        // return $endDate;

        $user = $request->user();
        if (is_null($user)) {
            return response(["message" => "Invalid token"], 422);
        }

//        $permissions = [];
//        $roles = $user->roles;
//        if($roles->isNotEmpty()){
//            foreach ($roles as $role){
//                $permissions = $role->permissions()->pluck('name')->toArray();
//            }
//        }

        $user_pr = auth()->user();
        $recruits = $user_pr->candidateRecruits()->whereDate('candidatecv_jobs.updated_at', '>=', $endDate)->where('candidatecv_jobs.action_id', '=', Action::PLACED_STATUS)->count();
        // $recruits = $endDate;
        $user = User::where('id', $user_pr->user_ID)->first();
        $response = [
            'id' => $user_pr->user_ID,
            'name' => $user->name,
            'photo' => $user_pr->photo,
            'phone' => $user_pr->phone,
            'email' => $user_pr->email,
            'job_title' => $user->job_title,
            'location' => $user->location,
            'google_calendar_access_token' => (is_null($user_pr->google_calendar_access_token)) ? false : true,
            'timezone_id' => (is_null($user_pr->timezone)) ? null : $user_pr->timezone_id,
            'timezone_name' => (is_null($user_pr->timezone)) ? null : $user_pr->timezone->name,
            'timezone' => (is_null($user_pr->timezone)) ? null : $user_pr->timezone->timezone,
            'roles' => $user_pr->roles()->select(['name', 'id'])->get(),
            'permissions' => $user_pr->getAllPermissions()->pluck('name')->all(),
            'recruits' => $recruits,
        ];
        return response($response, 200);

    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user_pr = $request->user();
        if (is_null($user_pr)) {
            return response(["message" => "Invalid token"], 422);
        }

        try {
            DB::transaction(function () use ($request, $user_pr) {
                $user_data = $request->only(['name', 'job_title', 'location']);
                if (is_array($user_data) && !empty($user_data)) {
                    User::where('id', $user_pr->user_ID)->update($user_data);
                }

                $user_pr_data = $request->only(['phone', 'timezone_id', 'int_code']);
                //Start Upload Photo
                if ($request->hasFile('photo')) {
                    $photo = $request->file('photo');
                    $photo_path = $photo->store(ApiAuthController::USER_PHOTO_UPLOAD_PATH, ApiAuthController::USER_PHOTO_UPLOAD_DISK);
                    $user_pr_data['photo'] = $photo_path;
                }
                //End


                if (is_array($user_pr_data) && !empty($user_pr_data)) {
                    UserPr::where('id', $user_pr->id)->update($user_pr_data);
                }
            });
            return response(["message" => "Profile successfully updated"], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = auth()->user();

        if ($user) {
            if (Hash::check($request['current_password'], $user->password)) {
                $user->password = Hash::make($request['new_password']);
                $response = $user->save();
                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" => 'User does not exist'];
            return response($response, 422);
        }

    }

    protected function getAuthUser (){
        return auth()->user()-toArray();
    }
}
