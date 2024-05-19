<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class NewPaymentReceived extends Notification
{
    use Queueable;

    private $user;

    private $payment;

    public function __construct(User $user, Payment $payment)
    {
        $this->user = $user;
        $this->payment = $payment;
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage())
            ->content($this->user->name.' ('.$this->user->email.') just loaded credit worth KES '.$this->payment->amount.' from '.$this->payment->phone.'!');
    }
}
