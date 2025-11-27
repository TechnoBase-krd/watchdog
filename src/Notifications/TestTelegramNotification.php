<?php

namespace Technobase\Watchdog\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class TestTelegramNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $message;

    public function __construct(string $message = 'Test notification from Watchdog')
    {
        $this->message = $message;

        // Set queue connection from config
        $queueConnection = config('watchdog.queue_connection');
        if ($queueConnection) {
            $this->onConnection($queueConnection);
        }
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->content("🧪 **Test Notification**\n\n{$this->message}\n\n✅ Watchdog Telegram integration is working!")
            ->token(config('watchdog.telegram_bot_token'),'')
            ->options(['parse_mode' => 'Markdown']);
    }
}
