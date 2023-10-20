<?php

namespace App\Notifications;

use App\Models\UserPr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Http\Controllers\HrTasks\Models\HrTask;

class HrTaskCreated extends Notification
{
    use Queueable;

    /**
     * @var HrTask
     */
    protected $task;

    /**
     * @var UserPr
     */
    protected $creator;

    /**
     * @var UserPr
     */
    protected $assigned_to;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($task, $creator, $assigned_to)
    {
        $this->task = $task;
        $this->creator = $creator;
        $this->assigned_to = $assigned_to;

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
        $taskboard_url = env('FRONTEND_URL').'/dashboard/todo';
        return (new MailMessage)
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject('New Task Notification')
                    ->greeting('Tere, ' . $this->assigned_to->user->name)
                    ->line($this->creator->user->name . ' lõi sinule ülesande: '. $this->task->description . '.')
                    ->action('Selle avamiseks kliki SIIA', $taskboard_url)
                    ->line('Ülesande tähtaeg on '. $this->task->deadline . '.')
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
        return [
            'title' => $this->creator->user->name . ' lõi Sinule uue käsu: ' . $this->task->description,
            'description' => $this->task->description,
            'deadline' => $this->task->deadline,
            'image' => $this->creator->photo
        ];
    }
}
