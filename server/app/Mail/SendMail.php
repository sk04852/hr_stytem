<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    public $emailSubject = "";
    public $emailBody = "";
    public $emailFrom = "";
    public $emailAttachments = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailFrom, $emailSubject, $emailBody, $emailAttachments)
    {
        $this->emailFrom = $emailFrom;
        $this->emailSubject = $emailSubject;
        $this->emailBody = $emailBody;
        $this->emailAttachments = $emailAttachments;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(is_array($this->emailAttachments) && !empty($this->emailAttachments)){
            foreach ($this->emailAttachments as $emailAttachment){
                $this->attach($emailAttachment->getPathName(), [
                    'as' => $emailAttachment->getClientOriginalName(),
                    'mime' => $emailAttachment->getMimeType(),
                ]);
            }
        }

        return $this->from($this->emailFrom)
            ->subject($this->emailSubject)
            ->view('emails.blueprint');
    }
}
