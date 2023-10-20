<?php

namespace App\Http\Controllers\Zoom;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tags\Models\Tag;
use App\Models\UserPr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Exception;
use App\Traits\ZoomJWT;

class ZoomController extends Controller
{
    use ZoomJWT;

    const MODULE_NAME = 'Zoom';
    const USER_NOT_FOUND = 'User not Found';
    const ZOOM_LINK_UPDATED = 'User Zoom Personal Link Updated';
    const ZOOM_LINK_UPDATED_FAILED = 'Unable to update User Zoom Personal Link';

    public function __construct(Tag $model)
    {
        parent::__construct($model);
    }

    public function index(Request $request)
    {
        $path = '/users';
        $response = $this->zoomPost($path, [
            'action' => 'create',
            'user_info' => [
                'email' => 'aneeqtariq9@gmail.com',
                'type' => 1
            ]
        ]);

        $data = json_decode($response->body(), true);
        dd($response, $response->status(), $data['id']);

        $data = json_decode($response->body(), true);

        return [
            'success' => $response->ok(),
            'data' => $data,
        ];
    }

    public function allUsers(Request $request)
    {
        $path = '/users';
        $response = $this->zoomGet($path);
        $data = json_decode($response->body(), true);

        return [
            'success' => $response->ok(),
            'data' => $data,
        ];
    }

    public function getUser(Request $request, $id)
    {
        $path = "/users/$id";
        $response = $this->zoomGet($path);
        $data = json_decode($response->body(), true);

        return [
            'success' => $response->ok(),
            'data' => $data,
        ];
    }

    public function updateUser(Request $request, $id)
    {
        $path = "/users/$id";
        $response = $this->zoomPatch($path, [
            'pmi' => 1234567890
        ]);
        $data = json_decode($response->body(), true);

        return [
            'success' => $response->status(),
            'data' => $data,
        ];
    }

    public function updateUserPersonalLink(Request $request, $id)
    {
        $get_user_path = "/users/$id";
        $response = $this->zoomGet($get_user_path);
        $data = json_decode($response->body(), true);
        if ($response->status() == 200) {
            if ($data['status'] == 'pending') {
                return $this->failed(['message' => 'User Status is Pending, Can not access Personal Link until he/she verify their account']);
            }


            if ($data['personal_meeting_url'] == '') {
                $update_user_path = "/users/$id";
                $updated_response = $this->zoomPatch($update_user_path, [
                    'pmi' => 1234567890
                ]);

                $response = $this->zoomGet($get_user_path);
                $data = json_decode($response->body(), true);
                if ($response->status() == 200) {
                    if ($data['personal_meeting_url'] != '') {
                        $user_pr = UserPr::where('zoom_id', $id)->first();
                        if (is_null($user_pr)) {
                            return $this->failed(['message' => ZoomController::USER_NOT_FOUND]);
                        }

                        $user_pr->zoom_personal_link = $data['personal_meeting_url'];
                        if ($user_pr->save()) {
                            return $this->created(['message' => ZoomController::ZOOM_LINK_UPDATED]);
                        } else {
                            return $this->failed(['message' => ZoomController::ZOOM_LINK_UPDATED_FAILED]);
                        }
                    }
                }


            } else {
                $user_pr = UserPr::where('zoom_id', $id)->first();
                if (is_null($user_pr)) {
                    return $this->failed(['message' => ZoomController::USER_NOT_FOUND]);
                }

                $user_pr->zoom_personal_link = $data['personal_meeting_url'];
                if ($user_pr->save()) {
                    return $this->created(['message' => ZoomController::ZOOM_LINK_UPDATED]);
                } else {
                    return $this->failed(['message' => ZoomController::ZOOM_LINK_UPDATED_FAILED]);
                }

            }
        }

    }


}

