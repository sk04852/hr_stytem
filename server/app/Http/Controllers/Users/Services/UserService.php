<?php

/**
 * UserService
 *
 * @copyright Copyright © 2023 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\Users\Services;

use App\Exceptions\ZoomAccountUnverified;
use App\Exceptions\ZoomIDNotFound;
use App\Traits\ZoomJWT;
use App\Models\UserPr;
use Exception;

class UserService
{
    use ZoomJWT;

    const ZOOM_REGISTERED_USER_FAILED = 'Zoom Id not found, Make sure you have successfully registered to zoom';
    const ZOOM_ID_NOT_FOUND = 'Zoom Id not found, Make sure you have successfully linked your account to zoom';
    const ZOOM_ACCOUNT_UNVIRIFIED = 'Your zoom status is Pending, Can not access Personal Link until he/she verify their account';

    public function linkZoomAccount($user_pr_id)
    {

        $user_object = UserPr::findOrFail($user_pr_id);

        $path = '/users';
        $response = $this->zoomPost($path, [
            'action' => 'create',
            'user_info' => [
                'email' => $user_object->email,
                'type' => 1
            ]
        ]);

        if ($response->status() == 201) {
            $zoom_data = json_decode($response->body(), true);
            $user_object->zoom_id = $zoom_data['id'];
            $user_object->save();
        } elseif ($response->status() == 409) {
            $get_user_path = "/users/$user_object->email";
            $response = $this->zoomGet($get_user_path);
            $data = json_decode($response->body(), true);
            if ($response->status() == 200) {
                if ($data['status'] == 'pending') {
                    throw new ZoomAccountUnverified(self::ZOOM_ACCOUNT_UNVIRIFIED);
                }


                if ($data['personal_meeting_url'] == '') {
                    $update_user_path = "/users/$user_object->zoom_id";
                    $updated_response = $this->zoomPatch($update_user_path, [
                        'pmi' => 1234567890
                    ]);

                    $response = $this->zoomGet($get_user_path);
                    $data = json_decode($response->body(), true);
                    if ($response->status() == 200) {
                        if ($data['personal_meeting_url'] != '') {
                            $user_object->zoom_id = $data['id'];
                            $user_object->zoom_personal_link = $data['personal_meeting_url'];
                            if ($user_object->save()) {
                                return $user_object;
                            } else {
                                return false;
                            }
                        }
                    }
                } else {
                    $user_object->zoom_id = $data['id'];
                    $user_object->zoom_personal_link = $data['personal_meeting_url'];
                    if ($user_object->save()) {
                        return $user_object;
                    } else {
                        return false;
                    }
                }
            }
        } else {
            throw new Exception(self::ZOOM_REGISTERED_USER_FAILED);
        }
    }

    /**
     * 
     */
    public function getZoomPersonalLink($user_pr_id)
    {
        $user_pr_object = UserPr::findOrFail($user_pr_id);
        
        // if (is_null($user_pr_object->zoom_id)) {
        //     throw new ZoomIDNotFound(self::ZOOM_ID_NOT_FOUND);
        // }

        $zoom_query = (is_null($user_pr_object->zoom_id)) ? $user_pr_object->email : $user_pr_object->zoom_id;
        
        $get_user_path = "/users/$zoom_query";
        $response = $this->zoomGet($get_user_path);
        $data = json_decode($response->body(), true);

        if ($response->status() == 404) {
            throw new ZoomIDNotFound(self::ZOOM_ID_NOT_FOUND);
        }
        
        if ($response->status() == 200) {
            if ($data['status'] == 'pending') {
                throw new ZoomAccountUnverified(self::ZOOM_ACCOUNT_UNVIRIFIED);
            }


            if ($data['personal_meeting_url'] == '') {
                $update_user_path = "/users/$zoom_query";
                $updated_response = $this->zoomPatch($update_user_path, [
                    'pmi' => 1234567890
                ]);

                $response = $this->zoomGet($get_user_path);
                $data = json_decode($response->body(), true);
                if ($response->status() == 200) {
                    if ($data['personal_meeting_url'] != '') {
                        if(is_null($user_pr_object->zoom_id)){
                            $user_pr_object->zoom_id = $data['id'];
                        }
                        $user_pr_object->zoom_personal_link = $data['personal_meeting_url'];
                        if ($user_pr_object->save()) {
                            return $user_pr_object;
                        } else {
                            return false;
                        }
                    }
                }
            } else {
                if(is_null($user_pr_object->zoom_id)){
                    $user_pr_object->zoom_id = $data['id'];
                }
                $user_pr_object->zoom_personal_link = $data['personal_meeting_url'];
                if ($user_pr_object->save()) {
                    return $user_pr_object;
                } else {
                    return false;
                }
            }
        }
    }
}
