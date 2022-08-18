<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrder implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $msg;
    public $owner;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($order, $msg,$client_id=null)
    {
        $this->order = ["id"=>$order->id,"ifclient"=>false];
        $this->msg = $msg;
        if($client_id!=null){
            $this->owner=$client_id;
            $this->order = ["id"=>$order->id,"ifclient"=>true];
        }else{
            $this->owner=$order->restorant->user->id;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('user.'.$this->owner);
    }

    public function broadcastAs()
    {
        return 'neworder-event';
    }
}
