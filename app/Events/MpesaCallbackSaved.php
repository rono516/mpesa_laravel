<?php

namespace App\Events;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MpesaCallbackSaved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $afterCommit = true;

    public $user;

    public $payment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Payment $payment)
    {
        $this->user = $user;
        $this->payment = $payment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('users.'.$this->user->uuid);
    }
}
