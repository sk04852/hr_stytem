<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkflowEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {     
        return $this->view('emails.workflow-email')
                    ->from($this->data['from'], $this->data['from_name'])
                    ->subject($this->data['subject'])
                    ->with($this->data);
    }
}