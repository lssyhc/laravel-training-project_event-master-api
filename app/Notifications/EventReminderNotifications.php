<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotifications extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Event $event) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reminder: Event Tomorrow - ' . $this->event->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a reminder that you\'re registered for an event tomorrow.')
            ->line('Event: ' . $this->event->name)
            ->line('Time: ' . $this->event->start_time->format('Y-m-d H:i'))
            ->line('Location: Online')
            ->action('View Event Details', url('/events/' . $this->event->id))
            ->line('We look forward to seeing you there!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_name' => $this->event->name,
            'start_time' => $this->event->start_time,
            'message' => 'Reminder: You have an event tomorrow!'
        ];
    }
}
