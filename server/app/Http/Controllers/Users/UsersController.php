<?php

namespace App\Http\Controllers\Users;

use App\Events\User\UserCreated;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Users\Requests\AssignUserPermissionsRequest;
use App\Http\Controllers\Users\Requests\ChangeRoleRequest;
use App\Http\Controllers\Users\Requests\CreateUserRequest;
use App\Http\Controllers\Users\Requests\DeleteUserRequest;
use App\Http\Controllers\Users\Requests\MarkNotificationReadRequest;
use App\Http\Controllers\Users\Requests\PartialUpdateUser;
use App\Http\Controllers\Users\Requests\UpdateUserRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\Services\UserService;
use App\Jobs\CreateZoomUser;
use App\Jobs\GetPersonalMeetingURL;
use App\Models\UserPr;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class UsersController extends Controller
{
    const MODULE_NAME = 'User';
    const USER_CREATED = 'New User created successfully';
    const USER_NOT_CREATED = 'Error in creating a User';
    const COLLECTION_NAME = 'Users';
    const USER_UPDATED = 'User updated successfully';
    const USER_NOT_UPDATED = 'Error in updating User';
    const USER_DELETED = 'User deleted successfully';
    const USER_NOT_DELETED = 'Error in deleting User';
    const USER_ALREADY_MARKED = 'User already marked';
    const USER_NOT_FOUND = 'User not found';
    const ASSIGN_USER_PERMISSIONS = 'Assigned User Permissions';
    const MARKED_NOTIFICATIONS_READ = 'Notifications successfully marked as Read';
    const MESSAGE_ZOOM_LINK_SUCCESS = 'Zoom link is successfull';

    protected $user_service;

    public function __construct(User $model, UserService $user_service)
    {
        parent::__construct($model);
        $this->user_service = $user_service;
    }

    public function index(Request $request)
    {
        try {

            $Users = $this->model
                ->orderBy($this->getSortBy(), $this->getSort())
                ->paginate($this->getPerPage());
            if ($Users->isNotEmpty()) {
                $response = $Users->toArray();
                foreach ($response['data'] as $key => $user) {
                    $temp_user_pr = UserPr::where('user_id', $user['id'])->first();
                    if (!is_null($temp_user_pr)) {
                        $response['data'][$key]['userpr_id'] = $temp_user_pr->id;
                        $response['data'][$key]['phone'] = $temp_user_pr->phone;
                        $response['data'][$key]['email'] = $temp_user_pr->email;
                        $response['data'][$key]['timezone_id'] = $temp_user_pr->timezone_id;
                        $response['data'][$key]['roles'] = $temp_user_pr->roles->toArray();
                    }
                }
                return $this->created([UsersController::COLLECTION_NAME => $response]);
            }

            return $this->noRecord(['message' => UsersController::RECORD_NOT_FOUND], 200);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function store(CreateUserRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'user_ID' => 1,
                    'name' => $request['name'],
                    'job_title' => $request['job_title'],
                    'location' => $request['location'],
                ]);

                $user_pr = UserPr::create([
                    'uuid' => Str::orderedUuid(),
                    'user_ID' => $user->id,
                    'phone' => $request['phone'],
                    'email' => $request['email'],
                    'password' => Hash::make($request['password']),
                    'timezone_id' => $request['timezone_id'],
                    'photo' => 'uploads/users/photos/avatar.jpg',

                    'permission_ID' => 1,
                    'int_code' => 1,
                    'userpr_ID' => 1
                ]);

                if ($request->has('role_id')) {
                    $user_pr->assignRole($request['role_id']);
                }

                UserCreated::dispatch($user_pr, $request['password']);
                // CreateZoomUser::dispatch($user_pr);
            });

            return $this->created(['message' => UsersController::USER_CREATED]);

        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function update(UpdateUserRequest $request)
    {
        $user = User::where('id', $request['id'])->first();
        $response = true;
        try {
            DB::transaction(function () use ($request, $user, &$response) {
                $user_data = $request->only(['name', 'job_title', 'location']);
                if (is_array($user_data) && !empty($user_data)) {
                    $response = User::where('id', $user->id)->update($user_data);
                }

                $user_pr_data = $request->only(['phone', 'email', 'password', 'timezone_id']);
                if (array_key_exists('password', $user_pr_data)) {
                    $user_pr_data['password'] = Hash::make($request['password']);
                }
                if (is_array($user_pr_data) && !empty($user_pr_data)) {
                    $response = UserPr::where('id', $request['userpr_id'])->update($user_pr_data);
                }


                if ($request->has('role_id')) {
                    $user_pr = UserPr::where('id', $request['userpr_id'])->first();
                    $response = $user_pr->syncRoles($request['role_id']);
                }

            });

            if ($response) {
                return $this->created(['message' => UsersController::USER_UPDATED]);
            }

            return $this->failed(['message' => UsersController::USER_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function partialUpdate(PartialUpdateUser $request)
    {
        $user_pr = UserPr::where('id', Auth::id())->first();
        if(is_null($user_pr)){
            throw new ModelNotFoundException(UsersController::USER_NOT_FOUND);
        }
        $response = false;
        try {
            DB::transaction(function () use ($request, $user_pr, &$response) {
                $user_pr_data = collect($request->only([
                    'phone',
                    'email',
                    'password',
                    'timezone_id',
                    'zoom_id',
                    'zoom_personal_link',
                    'skype_id',
                    'skype_personal_link',
                    'google_calendar_access_token',
                    'google_calendar_refresh_token',
                    'google_calendar_user_account_info'
                ]));

                $user_pr->fill($user_pr_data->whereNotNull()->toArray());
                $user_pr->save();


                $user_data =  collect($request->only(['name', 'job_title', 'location']));
                $user_pr->user->fill($user_data->whereNotNull()->toArray())->save();

                $response = true;
            });

            if ($response) {
                return $this->created(['message' => UsersController::USER_UPDATED]);
            }

            return $this->failed(['message' => UsersController::USER_NOT_UPDATED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroy(DeleteUserRequest $request)
    {
        try {
            $user_ids = $request->get('users');
            DB::transaction(function () use ($request, $user_ids) {
                if (is_array($user_ids) && !empty($user_ids)) {
                    foreach ($user_ids as $user_id) {
                        $user = User::find($user_id['id']);
                        $user_pr = UserPr::find($user_id['userpr_id']);
                        if(is_null($user) && is_null($user_pr)){
                            throw new ModelNotFoundException('User Not Found');
                        }

                        //Delete assigned tasks to this user
                        $user_pr->hrTasks()->detach();

                        //Delete Google Calendar
                        if($user_pr->googleCalendars->isNotEmpty()){
                            foreach ($user_pr->googleCalendars as $calendar){
                                $calendar->events()->delete();
                                $calendar->delete();
                            }
                        }


                        //Delete Shared Google Calendars
                        $user_pr->hrTasks()->detach();

                        //Delete Roles
                        $user_pr->roles()->detach();
                        $user->delete();
                        $user_pr->delete();
                    }
                }
            });

            return $this->created(['message' => UsersController::USER_DELETED]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function changeRole(ChangeRoleRequest $request)
    {
        try {
            $user_pr = UserPr::where('id', $request->user_pr_id)->first();

            $user_pr->syncRoles($request->role_id);

            return $this->created(['message' => 'User role changes']);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function assignUserPermissions(AssignUserPermissionsRequest $request)
    {
        try {
            $user_pr = UserPr::where('id', $request->user_id)->first();
            if (is_null($user_pr)) {
                return $this->noRecord(['message' => UsersController::RECORD_NOT_FOUND]);
            } else {
                $user_pr->syncPermissions($request->permissions);
                return $this->created(['message' => UsersController::ASSIGN_USER_PERMISSIONS]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function show($id)
    {
        try {
            $user_pr = UserPr::where('id', $id)->first();
            if (is_null($user_pr)) {
                return $this->noRecord(['message' => UsersController::RECORD_NOT_FOUND]);
            } else {
                $user = User::where('id', $user_pr->user_ID)->first();
                $response = [
                    'id' => $user_pr->user_ID,
                    'user_pr_id' => $user_pr->id,
                    'name' => $user->name,
                    'photo' => $user_pr->photo,
                    'phone' => $user_pr->phone,
                    'email' => $user_pr->email,
                    'job_title' => $user->job_title,
                    'location' => $user->location,
                    'timezone_id' => (is_null($user_pr->timezone)) ? null : $user_pr->timezone_id,
                    'timezone_name' => (is_null($user_pr->timezone)) ? null : $user_pr->timezone->name,
                    'timezone' => (is_null($user_pr->timezone)) ? null : $user_pr->timezone->timezone,
                    'roles' => $user_pr->roles()->select(['name', 'id'])->get(),
                    'permissions' => $user_pr->getAllPermissions()
                ];
                return $this->created(['user' => $response]);
            }
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function unreadNotificationsCount(Request $request)
    {
        try {
            $user = $request->user();
            if (is_null($user)) {
                return $this->noRecord(['message' => UsersController::RECORD_NOT_FOUND]);
            }

            return $this->created(['unread_notifications' => $user->unreadNotifications->count()]);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function notifications(Request $request, $offset = 0, $limit = 3)
    {
        try {
            $user = $request->user();
            if (is_null($user)) {
                return $this->noRecord(['message' => UsersController::RECORD_NOT_FOUND]);
            }

            $offset = (int)$offset;
            $notifications = $user->notifications()
                ->when(($offset > 0), function ($query) use ($offset) {
                    return $query->offset($offset);
                })
                ->limit($limit)
                ->get();
            $notification_data = [];
            foreach ($notifications as $notification) {
                $temp_data = [
                    'id' => $notification->id,
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,

                ];

                if (is_array($notification->data) && !empty($notification->data)) {
                    foreach ($notification->data as $key => $value) {
                        $temp_data[$key] = $value;
                    }
                }

                $notification_data[] = $temp_data;

            }

            $offset = (!empty($notification)) ? $offset + $limit : $offset;
            return $this->created(['notifications' => $notification_data, 'offset' => $offset]);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }

    }

    public function markNotificationRead(MarkNotificationReadRequest $request)
    {
        try {
            $notifications = $request->user()->unreadNotifications()->whereIn('id', $request->notification_id)->get();

            if ($notifications->isNotEmpty()) {
                foreach ($notifications as $notification) {
                    $notification->markAsRead();
                }
            }

            return $this->created([
                'message' => UsersController::MARKED_NOTIFICATIONS_READ,
                'latest_unread_notifications' => $request->user()->unreadNotifications->count()
            ]);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }

    }

    public function markAllkNotificationRead(Request $request){
        try {
            $user = $request->user();
            if (is_null($user)) {
                return $this->noRecord(['message' => UsersController::RECORD_NOT_FOUND]);
            }

            $user->unreadNotifications->markAsRead();

            return $this->created([
                'message' => UsersController::MARKED_NOTIFICATIONS_READ,
                'latest_unread_notifications' => $user->unreadNotifications->count()
            ]);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function linkZoomAccount(Request $request, $id)
    {
        try {
            $this->user_service->linkZoomAccount($id);
            return $this->created([
                'message' => UsersController::MESSAGE_ZOOM_LINK_SUCCESS
            ]);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }

    public function getZoomPersonalLink(Request $request){
        try {
            $loggedin_user_id = Auth::id();
            // dd($loggedin_user_id);
            $response = $this->user_service->getZoomPersonalLink($loggedin_user_id);
            return $this->created([
                'message' => UsersController::MESSAGE_ZOOM_LINK_SUCCESS
            ]);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
}

