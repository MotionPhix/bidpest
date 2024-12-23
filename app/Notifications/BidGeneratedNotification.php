<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BidGeneratedNotification extends Notification
{
  use Queueable;

  protected $bid;

  public function __construct($bid)
  {
    $this->bid = $bid;
  }

  public function via($notifiable)
  {
    return ['mail']; // You can add other channels like database, broadcast, etc.
  }

  public function toMail($notifiable)
  {
    return (new MailMessage)
      ->subject('Your Bid has been Generated')
      ->line('Your bid for the tender "' . $this->bid->tenderDocument->title . '" has been successfully generated.')
      ->action('View Bid', url('/bids/' . $this->bid->id))
      ->line('Thank you for using our application!');
  }
}
