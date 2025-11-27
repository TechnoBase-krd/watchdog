<?php

namespace Technobase\Alert\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramMessage;

class TelegramErrorNotification extends Notification implements ShouldQueue
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

    private string $title;
    private string $message;
    private array $context;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $message, array $context = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->context = $context;

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
        $content = "🚨 **{$this->title}**\n\n";
        $content .= "**Message**: {$this->message}\n\n";

        // Add context details
        if (!empty($this->context['file'])) {
            $content .= "**File**: `{$this->context['file']}:{$this->context['line']}`\n";
        }

        if (!empty($this->context['url'])) {
            $content .= "**URL**: `{$this->context['url']}`\n";
        }

        if (!empty($this->context['user_id'])) {
            $content .= "**User**: `ID: {$this->context['user_id']}`\n";
        }

        // Add environment info
        if (!empty($this->context['environment'])) {
            $content .= "**Environment**: `{$this->context['environment']}`\n";
        }

        // Add truncated stack trace
        if (!empty($this->context['trace'])) {
            $trace = $this->context['trace'];
            $traceLines = explode("\n", $trace);
            $maxLines = config('alert.trace_lines', 10);
            $truncatedTrace = implode("\n", array_slice($traceLines, 0, $maxLines));

            $content .= "\n**Trace**:\n```\n{$truncatedTrace}\n```";
        }
        return TelegramMessage::create()
            ->content($content)
            ->token(config('alert.bot_token', ''))
            ->options(['parse_mode' => 'Markdown']);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send Telegram error notification', [
            'title' => $this->title,
            'message' => $this->message,
            'context' => $this->context,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // You could also:
        // - Send an email notification as fallback
        // - Store in database for manual review
        // - Alert via alternative channel (Slack, email, etc.)
    }
}
