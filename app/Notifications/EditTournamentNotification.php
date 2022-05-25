<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EditTournamentNotification extends Notification
{
    use Queueable;
    protected $tournament;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($tournament)
    {
        $this->tournament=$tournament;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }


    public function toDatabase($notifiable)
    {
        return[
            'title'=>'Modified Tournament',
            'body'=>'Tournament has been Modified'.' | '.
            'Email : '.$this->tournament->email.' | '.
            'the Tournament is gonna be on : '.$this->tournament->date.' | '.
            'Location : '.$this->tournament->location,
            'image'=>'',
            'url'=>url('http://localhost:3000'),
            'tournament'=>$this->tournament,


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
