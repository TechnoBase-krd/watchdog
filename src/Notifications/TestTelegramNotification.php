<?php

namespace Technobase\Alert\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramMessage;

class TestTelegramNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public int $backoff = 60;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 30;

    private string $message;

    public function __construct(string $message = 'Test notification from Alert')
    {
        $this->message = $message;

        // Set queue connection from config
        $queueConnection = config('alert.queue_connection');
        if ($queueConnection) {
            $this->onConnection($queueConnection);
        }

        // If queue is disabled, set connection to 'sync' to force immediate execution
        if (!config('alert.queue', true)) {
            $this->onConnection('sync');
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
            ->content("🧪 **Test Notification**\n\n{$this->message}\n\n✅ Alert Telegram integration is working!")
            ->token(config('alert.bot_token', ''))
            ->options(['parse_mode' => 'Markdown']);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send Telegram test notification', [
            'message' => $this->message,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
