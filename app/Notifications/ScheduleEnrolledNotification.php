<?php

namespace App\Notifications;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleEnrolledNotification extends Notification
{
    use Queueable;
    protected $schedule;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Schedule $schedule)
    {
        //
        $this->schedule=$schedule;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject("Session Reserved")
                    ->greeting("Hi! $notifiable->name,")
                    ->line('new session has been reserved by '.$this->schedule->user->name)
                    ->line('Email : '.$this->schedule->user->email)
                    ->line('the session starts at : '.$this->schedule->startDate)
                    ->line('and ends at : '.$this->schedule->endDate)
                    ->action('See your Reserved Sessions', url('http://localhost:3000/RCourses'))
                    ->line('Thank you!');
    }
    public function toDatabase($notifiable)
    {
        return[
            'title'=>'Session Reserved',
            'body'=>'new session has been reserved by '.$this->schedule->user->name.' |
            Email : '.$this->schedule->user->email.' |
            the session starts at : '.$this->schedule->startDate.' ->
            and ends at : '.$this->schedule->endDate,
            'image'=>'',
            'url'=>url('http://localhost:3000/RCourses'),
            'schedule'=>$this->schedule,


        ];
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
            //
        ];
    }
}
