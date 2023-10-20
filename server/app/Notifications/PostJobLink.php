<?php

namespace App\Notifications;

use App\Http\Controllers\Companies\Models\CompanyContact;
use App\Http\Controllers\Jobs\Models\JobPr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostJobLink extends Notification
{
    use Queueable;

    /**
     * @var JobPr
     */
    protected $job_pr;

    /**
     * @var CompanyContact
     */
    protected $company_contact;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(JobPr $job_pr, CompanyContact $company_contact)
    {
        $this->job_pr = $job_pr;
        $this->company_contact = $company_contact;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $posted_job_url = env('FRONTEND_URL').'/hr-jobs/view/'. $this->job_pr->id;
        return (new MailMessage)
                    ->line($this->company_contact->name . ' saatis uue tööpakkumise päringu. Palun vaata üle.')
                    ->action('View Job Posted', url($posted_job_url))
                    ->salutation(" ");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $posted_job_url = env('FRONTEND_URL').'/hr-jobs/view/'. $this->job_pr->id;
        return [
            'title' => $this->company_contact->name . ' saatis uue tööpakkumise päringu. Palun vaata üle',
            'description' => '<a href='. $posted_job_url .'>View Job</a>',
            // 'deadline' => $this->task->deadline,
            // 'image' => $this->creator->photo
        ];
    }
}
