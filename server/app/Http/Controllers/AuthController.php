<?php

namespace App\Http\Controllers;

use App;
use App\Events\TwoFactorAuthEnabledEvent;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\Users\Requests\UserLoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Google2FA;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\LoginHistory\Models\LoginHistory;
use App\Http\Controllers\Users\Models\TemporaryUser;
use App\Http\Controllers\Users\Models\UserExtraInfo;
use App\Http\Controllers\Users\Requests\TemporaryLoginRequest;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $jwt;
    private $google2FA;
    private $verifyCode_ = 1;

    public function __construct(
        JWTAuth $jwt,
        Google2FA $google2FA
    ) {
        $this->jwt = $jwt;
        $this->google2FA = $google2FA;
    }

    public function login(UserLoginRequest $request)
    {

        $userExtraInfo = UserExtraInfo::where('phone_number', $request->phone_number)->first();

        if (!$userExtraInfo) {
            return response()->json(['error' => 'Login or password incorrect', 'code' => 'incorrect_account'], 401);
        }

        $user = User::where('id', $userExtraInfo->user_id)->first();

        $credentials = ['email' => $user->email, 'password' => $request->password];


        if (!$token = $this->guard()->attempt($credentials)) {
            return response()->json(['error' => 'Login or password incorrect', 'code' => 'incorrect_account'], 401);
        }

        if (!$user->isActive()) {
            return response()->json(['error' => 'Your account is not active', 'code' => 'account_not_active'], 422);
        }

        if (!$user->isEmailVerified()) {
            return response()->json(['error' => 'Your account email is not verified', 'code' => 'email_not_verified'], 422);
        }

        if ($user->checkGoogle2FA()) {

            return response()->json(['error' => 'Please verify 2fa Token', 'otp_required' => true], Response::HTTP_FORBIDDEN);
        } else if ($user->checkEmail2FA()) {
            $code = User::generateEmailCode();
            $user->enable2FAWithEmail($code);
            event(new TwoFactorAuthEnabledEvent($user, $code, null, $this->verifyCode_));
            return response()->json(['error' => 'Please verify 2fa Token', 'otp_required' => true], Response::HTTP_FORBIDDEN);
        } else {

            $this->postLoginActions($credentials);
            return $this->respondWithToken($token, $user);
        }
    }

    public function validateToken(Request $request)
    {
        $data = [];
        $this->getValidatedData($request);

        if (!$token = $this->jwt->attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Username or password incorrect'], Response::HTTP_FORBIDDEN);
        }
        $user = User::where('email', $request->email)->first();

        if ($user->type_of_authentication === 'email') {
            $emailCode = $user->email_code;
            $otp = $request->token;
            if ($emailCode != (string)$otp) {
                return response()->json(['error' => 'Invalid OTP, please try again'], 422);
            } else {
                return $this->respondWithToken($token, $user);
            }
        } else if ($user->type_of_authentication === 'Google Authenticator') {
            $secret = $user->secret_2fa;
            $otp = $request->token;
            if (!$this->google2FA->verifyKey($secret, (string)$otp)) {
                return response()->json(['error' => 'Invalid OTP, please try again'], 422);
            } else {
                return $this->respondWithToken($token, $user);
            }
        } else {
            return $this->respondWithToken($token, $user);
        }
    }

    public function getValidatedData(Request $request)
    {
        return $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);
    }

    public function saveHistory(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();
        $history = new LoginHistory();
        $history->type_id = $user->type_id;
        // if (isset($credentials['company_id'])) {
        //     $history->company_id = $this->companyId();
        // }
        // $history->company_id = $this->companyId();
        $history->user_id = $user->id;
        $history->status = 'LoggedIn';
        $history->ip = request()->ip();
        $history->login_time = now();
        $history->save();
    }

    public function logoutHistory($data)
    {
        $history = new LoginHistory();
        $history->company_id = $this->companyId();
        $history->type_id = $data->type_id;
        $history->user_id = $data->id;
        $history->status = 'LoggedOut';
        $history->ip = request()->ip();
        $history->logout_time = now();
        $history->save();
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function temporaryLogin(TemporaryLoginRequest $request)
    {

        $tempUser =  $this->fetchTemporaryUser($request->user_id);

        $credentials = [
            'email' => $tempUser->user->email,
            'password' => $tempUser->pass_phrase
        ];

        if (!$token = $this->guard()->attempt($credentials)) {
            return response()->json(['error' => 'Login or passord incorrect', 'code' => 'incorrect_account'], 401);
        }

        return $this->respondWithToken($token, $tempUser->user);
    }

    public function fetchTemporaryUser(int $userId)
    {
        return TemporaryUser::where('user_id', $userId)->first();
    }

    public function logout()
    {
        $data = auth()->user();
        if ($data) {
            $this->logoutHistory($data);
        }
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token, $user)
    {
        $code = ($token == false) ? 401 : 200;
        $data = [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
        $data['status'] = ($user->company_id != null) ? 'setup-completed' : 'incomplete-setup';

        $data['user'] = ['id'=> $user->id,'full_name'=> $user->full_name,'email'=>$user->email];

        return response()->json($data, $code);
    }

    private function postLoginActions(array $credentials)
    {
        User::where('email', $credentials['email'])->update(['last_login_at' => now()]);
        $this->saveHistory($credentials);
    }

    public function guard()
    {
        return Auth::guard();
    }

    public function getTokenById(int $id)
    {
        $user = User::where('id', $id)->first();
        $jwtAuth = App::make('Tymon\JWTAuth\JWTAuth');
        $token = $jwtAuth->fromUser($user);
        return response()->json(['token' => $token]);
    }
}
