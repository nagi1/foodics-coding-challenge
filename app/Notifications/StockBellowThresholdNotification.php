<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockBellowThresholdNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $name, protected int $stock)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $threshold = config('app.stock_percentage_threshold', 50);

        return (new MailMessage)
                    ->subject("{$this->name} is bellow the {$threshold}% threshold")
                    ->line("The ingredient {$this->name} is bellow the threshold. Current stock: {$this->stock}.")
                    ->action("Go to the ingredient's page", url('/ingredients'))
                    ->line('To order more ingredients, please contact the supplier.');
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
