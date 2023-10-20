<?php namespace App\Http\Controllers\Leads\Requests;

use Illuminate\Foundation\Http\FormRequest;


class FileRequest extends FormRequest {

    public function authorize(){
        return true;
    }

    public function rules()
	{

        return [

          'csv_file' => ['required','file'],
          'has_headers' => ['required', 'boolean'],
        ];
    }

   }
