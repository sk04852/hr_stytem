<?php
/**
 * SendMail
 *
 * @copyright Copyright © 2022 Aneeq Tariq. All rights reserved.
 * @author    aneeqtariq_143@yahoo.com
 */

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Companies\Models\CompanyContact;
use App\Http\Controllers\Companies\Models\CompanyPr;
use App\Http\Controllers\Mail\Models\Template;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mail\Requests\SendCompanyMailRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Mail\Requests\Template\SendMailRequest;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail as SendMailService;
use App\Lib\TemplateParser;
use App\Models\AutoGenerateURL;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class SendMail extends Controller
{
    public $templateId;
    public $email = "";

    const MODULE_NAME = 'Mail';
    const COLLECTION_NAME = 'SendMail';
    const TEMPLATE_NOT_FOUND = 'Template not found';
    const EMAIL_SENT = 'Email Sent';
    const CANDIDATE_FILES_UPLOAD_ERROR = 'Candidate upload files not found';
    const CANDIDATE_FILES_INVALID_FORMAT = 'Candidate invalid upload files. only (docx, xls, csv, zip, pdf, jpg, png) allowed';
    const INVALID_REQUEST = 'Request not valid. Some of the request parameters not correct';

    public function __construct(Template $model)
    {
        parent::__construct($model);
    }

    public function sendMail(SendMailRequest $request)
    {
        try {
            $to_emails = explode(',', str_replace(' ', '', $request->to));
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

            if ($request->hasFile('files')) {
                $allowedfileExtension = ['docx', 'xls', 'csv', 'zip', 'pdf', 'PDF', 'jpg', 'JPG', 'png'];
                $files = $request->file('files');
                $errors = [];


                foreach ($files as $file) {
                    $extension = strtolower($file->getClientOriginalExtension());
                    $check = in_array($extension, $allowedfileExtension);

                    if (!$check) {
                        return $this->failed(['message' => sendMail::CANDIDATE_FILES_INVALID_FORMAT]);
                    }else{
                        array_push($attachments, $file);
                    }
                }
            }

            Mail::to($valid_to_email)->send(new SendMailService($request->from, $request->subject, $request->body, $attachments));
            return $this->created(['message' => SendMail::EMAIL_SENT, 'invalid_emails' => $invalid_to_email]);

        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }


    }

    public function sendCompanyMail(SendCompanyMailRequest $request)
    {
        try {
            $auto_generated_url_object = AutoGenerateURL::findOrFail($request->auto_generated_url_id);
            $company_contact_object = CompanyContact::findOrFail($request->company_contact_id);
            
            $to_emails = explode(',', str_replace(' ', '', $company_contact_object->email));
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

            if ($request->hasFile('files')) {
                $allowedfileExtension = ['docx', 'xls', 'csv', 'zip', 'pdf', 'PDF', 'jpg', 'JPG', 'png'];
                $files = $request->file('files');
                $errors = [];


                foreach ($files as $file) {
                    $extension = strtolower($file->getClientOriginalExtension());
                    $check = in_array($extension, $allowedfileExtension);

                    if (!$check) {
                        return $this->failed(['message' => sendMail::CANDIDATE_FILES_INVALID_FORMAT]);
                    }else{
                        array_push($attachments, $file);
                    }
                }
            }

            Mail::to($valid_to_email)->send(new SendMailService($request->from, $request->subject, $request->body, $attachments));
            $auto_generated_url_object->company_contact_id = $company_contact_object->id;
            $auto_generated_url_object->save();
            return $this->created(['message' => SendMail::EMAIL_SENT, 'invalid_emails' => $invalid_to_email]);
        } catch (ModelNotFoundException $ex) {
            Log::error($ex);
            return $this->noRecord(['message' => self::INVALID_REQUEST]);
        } catch (Exception $ex) {
            Log::error($ex);
            return $this->serverError($ex);
        }
    }
}
