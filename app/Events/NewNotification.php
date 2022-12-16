<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $msg;
    public $user;
    public $ruta;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($order,$msg,$ruta,$client_id=null)
    {
        $this->user=$client_id;
        $this->msg = $msg;
        $this->order = ["id"=>$order->id,"ifclient"=>true,"ruta"=>$ruta];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('user.'.$this->user);
    }

    public function broadcastAs()
    {
        return 'neworder-event';
    }
}
