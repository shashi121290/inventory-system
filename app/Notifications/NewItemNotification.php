<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewItemNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    protected $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Item Created')
            ->greeting('Hello!')
            ->line('A new item has been created.')
            ->action('View Item', url('/api/items'))
            ->line('Thank you for using our application.');
    } 

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'item_id' => $this->item['id'],
            'item_name' => $this->item['name']
        ];
    }
}
