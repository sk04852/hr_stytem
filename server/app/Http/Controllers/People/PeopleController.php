<?php

namespace App\Http\Controllers\People;

use App\Events\ForgotPasswordEvent;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use App\Http\Services\EmailsService;
use App\Notifications\ForgotPasswordNotification;
use App\Http\Controllers\People\Models\Person;
use App\Http\Controllers\People\Requests\PersonCreateRequest as CreateRequest;
use App\Http\Controllers\People\Requests\PersonForgotPasswordRequest;
use App\Http\Controllers\People\Requests\PersonResetPasswordRequest;
use App\Http\Controllers\People\Requests\PersonChangePasswordRequest;
use App\Http\Controllers\People\Requests\PersonMassDeleteRequest;
use App\Http\Controllers\People\Requests\PersonUpdateRequest;
use App\Http\Controllers\Users\Models\TemporaryUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PeopleController extends Controller
{
    const USERS_SUCCESS = "Successful.";
    const USERS_NOT_FOUND = "No Users exists.";
    const SIGNUP_SUCCESS = "Sign up successfully.";
    const SIGNUP_ERROR = "Signup failed.";
    const PROFILE_SUCCESS = "Successfull.";
    const PROFILE_ERROR = "failed.";
    const UPDATE_SUCCESS = "Updated successfully.";
    const UPDATE_ERROR = "Updation Failed.";
    const FORGOT_SUCCESS = "Reset password link sent to email.";
    const FORGOT_ERROR = "Email address not exist.";
    const RESET_SUCCESS = "Reset password successfully.";
    const RESET_ERROR = "Reset password failed.";
    const RESET_ERROR_TOKEN = "Token Expired, request a new password reset link.";
    const DELETE_SUCCESS = "Successfully deleted.";
    const DELETE_ERROR = "Deletion Failed.";
    const ACCOUNT_PROFILE = 'Successfull.';
    const ACCOUNT_REGISTER = 'Successfully Registered.';
    const ACCOUNT_RESEND_TOKEN = 'Successfully resent token.';
    const ACCOUNT_UPDATED = 'Successfully Updated.';
    const VERIFY_EMAIL_ERROR_TOKEN = "Token Expired, request a new verify link.";
    const VERIFY_EMAIL_SUCCESS = "Successfully Verified.";
    const CHANGE_PASSWORD_SUCCESS = "Successfully changed.";
    const CHANGE_PASSWORD_ERROR = "Please try again.";
    const MODULE_NAME = 'people';
    const COLLECTION_NAME = 'records';
    protected $emailsService;

    public function __construct(Person $model, EmailsService $emailsService)
    {
        parent::__construct($model);
        $this->model = $model;
        $this->emailsService = $emailsService;
    }

    public function listPeople()
    {
        try {
            $users = $this->model
                        ->orderBy($this->getSortBy(), $this->getSort())
                        ->paginate($this->getPerPage());
            if ($users) {
                return $this->created(["message" => PeopleController::USERS_SUCCESS, PeopleController::COLLECTION_NAME => $users]);
            } else {
                return $this->noRecord(['messsage'=> PeopleController::USERS_NOT_FOUND]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    protected function preRegistration(Person $model) {
        // Child class has the implementation.
    }

    public function register(CreateRequest $request,TemporaryUser $temporaryUser)
    {
        try {
            $data = $request->all();
            $this->model = $this->model->fill($data);
            if ($request->user_type == 'student') {
                $this->model->type_id = 4;
            } else {
                $this->model->type_id = 3;
            }
            $this->model = $this->preRegistration($this->model);
            $this->model->password = Hash::make($data["password"]);
            if ($this->model->save()) {
                $this->saveTemporaryUser($data["password"], $temporaryUser);
                $extraInformation = $this->postRegistration($this->model);
                $extraInformation->update(['phone_number'=> $request->phone_number]);
                return $this->created(["message" => PeopleController::SIGNUP_SUCCESS, "profile" => $this->model]);
            } else {
                return $this->failed(["error" => PeopleController::SIGNUP_ERROR]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function saveTemporaryUser(string $password,TemporaryUser $temporaryUser)
    {
        $temporaryUser->user_id = $this->model->id;
        $temporaryUser->pass_phrase = $password;
        $temporaryUser->save();
        return $temporaryUser;
    }

    protected function postRegistration(Person $model) {
        // Child class has the implementation.
    }

    protected function preUpdate(PersonUpdateRequest $request, Person $model) {
        // Child class has the implementation.
    }

    public function update(PersonUpdateRequest $request)
    {
        try {
            if($request->has('password') && $request->get('password') == "") {
                unset($data['password']);
                unset($data['password_confirmation']);
            }
            $this->model = $this->findById($request->get('id'));
            $this->preUpdate($request, $this->model);
            if ($this->model->update($request->all())) {
                $this->postUpdate($request, $this->model);
                return $this->created(["message" => PeopleController::UPDATE_SUCCESS, "profile" => $this->model]);
            } else {
                return $this->failed(["error" => PeopleController::UPDATE_ERROR]);
            }
        } catch(ModelNotFoundException $modelNotFoundException) {
            return $this->noRecord(["message"=> 'User not found, invalid user id']);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    protected function postUpdate(PersonUpdateRequest $request, Person $model) {
        // Child class has the implementation.
    }

    public function forgotPassword(PersonForgotPasswordRequest $request)
    {
        try {
            $data = $request->all();
            $user = $this->model->where('email', $data['email'])->first();
            if ($user) {
                $token = Str::random(10);
                $user->setPasswordRecoveryToken($token);
                // $user->notify(new ForgotPasswordNotification($user, $token));
                event(new ForgotPasswordEvent($user, $token));
                return $this->created(["message" => PeopleController::FORGOT_SUCCESS]);
            } else {
                return $this->failed(["error" => PeopleController::FORGOT_ERROR]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    protected function preDelete(Person $model) {
        // Child class has the implementation.
    }

    public function destroy(int $id)
    {
        try {
            $this->model = $this->model->where('id', $id)->first();
            $this->model = $this->preDelete($this->model);
            if ($this->model->delete()) {
                $this->postDelete($this->model);
                return $this->created(["message" => PeopleController::DELETE_SUCCESS]);
            } else {
                return $this->failed(["error" => PeopleController::DELETE_ERROR]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    protected function postDelete(Person $model) {
        // Child class has the implementation.
    }

    public function performMassDelete(PersonMassDeleteRequest $request) {
        try {
            foreach($request->get('id') as $id) {
                $this->destroy($id);
            }
            return $this->created(['message'=> PeopleController::RECORD_DELETED]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function resetPassword(PersonResetPasswordRequest $request)
    {
        try {
            $data = $request->all();
            $User = $this->model->where('password_recovery_token', $data['token'])->first();
            $tokenCreatedAt = new \DateTime($User->password_recovery_token_created_at);
            $now = new \DateTime();

            if ($now->diff($tokenCreatedAt)->format('%i') > 60) {
                return $this->failed(["error" => PeopleController::RESET_ERROR_TOKEN]);
            }

            if ($User->setPassword($data["password"], 0, true)) {
                $this->notify($this->model);
                $this->postResetPassword($User);
                return $this->created(["message" => PeopleController::RESET_SUCCESS]);
            } else {
                return $this->failed(["error" => PeopleController::RESET_ERROR]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function changePassword(PersonChangePasswordRequest $request) {
        try {
            $data = $request->all();
            $user = $this->findOneById($this->userId());
            if ($user->setPassword($data["password"], $this->userId(), true)) {
                $this->postChangePassword($user);
                return $this->created(["message" => PeopleController::RESET_SUCCESS]);
            } else {
                return $this->failed(["error" => PeopleController::RESET_ERROR]);
            }
        } catch(ModelNotFoundException $modelNotFoundException) {
            return $this->noRecord(["message"=> 'User not found, invalid user id']);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    protected function postChangePassword(Person $model) {
        // Child class has the implementation.
    }

    public function notify()
    {
        # code...
    }

    public function findById(int $id, $with = [])
    {
        $this->model = $this->model->where('id', $id)->with($with)->firstOrFail();
        return $this->model;
    }
    protected function postResetPassword(Person $model) {
        // Child class has the implementation.
    }
}
