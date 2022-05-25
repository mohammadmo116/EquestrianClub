<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class chatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    public $firstUsername;
    public $SecondUsername;
    public $id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message,$firstUsername,$SecondUsername,$id)
    {
        $this->message = $message;
        $this->firstUsername = $firstUsername;
        $this->SecondUsername = $SecondUsername;
        $this->id = $id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('chat-channel.'.$this->SecondUsername.'.'.$this->firstUsername );
    }
    public function broadcastAs()
    {
        return 'chat_event';
    }
}
