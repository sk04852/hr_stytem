<?php
/**
 * MailSerivce
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\Mail\Services;

use App\Http\Controllers\Mail\SendMail;
use App\Mail\SendMail as SendMailService;
use Illuminate\Support\Facades\Mail;

class MailService
{

    public function sendMail($to, $from, $subject, $body, $files = []){
        $to_emails = explode(',', str_replace(' ', '', $to));
        $valid_to_email = [];
        $attachments = [];
        $invalid_to_email = [];
        if(is_array($to_emails) && !empty($to_emails)){
            foreach ($to_emails as $email){
                if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                    array_push($valid_to_email, $email);
                }else{
                    array_push($invalid_to_email, $email);
                }
            }
        }

//        if ($files->hasFile('files')) {
//            $allowedfileExtension = ['docx', 'xls', 'csv', 'zip', 'pdf', 'jpg', 'png'];
//            $files = $files->file('files');
//            $errors = [];
//
//
//            foreach ($files as $file) {
//                $extension = $file->getClientOriginalExtension();
//                $check = in_array($extension, $allowedfileExtension);
//
//                if (!$check) {
//                    return $this->failed(['message' => sendMail::CANDIDATE_FILES_INVALID_FORMAT]);
//                }else{
//                    array_push($attachments, $file);
//                }
//            }
//        }

        return Mail::to($valid_to_email)->send(new SendMailService($from, $subject, $body, $files));
    }
}
