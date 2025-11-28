<?php

namespace Technobase\Alert;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Technobase\Alert\Notifications\TelegramErrorNotification;
use Throwable;

class AlertServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/alert.php',
            'alert'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/alert.php' => config_path('alert.php'),
            ], 'alert-config');
        }

        // Register exception handler if enabled
        if (config('alert.enabled', true)) {
            $this->registerExceptionHandler();
        }
    }

    /**
     * Register the exception handler for Telegram notifications.
     */
    protected function registerExceptionHandler(): void
    {
        $this->app->make('Illuminate\Contracts\Debug\ExceptionHandler')
            ->reportable(function (Throwable $e) {
                // Check if we should send notification based on environment
                $enabledEnvironments = config('alert.enabled_environments', ['production', 'staging']);

                if (! in_array(app()->environment(), $enabledEnvironments)) {
                    return;
                }

                // Get chat ID from config
                $chatId = config('alert.chat_id');

                if (empty($chatId)) {
                    return;
                }

                try {
                    $notification = new TelegramErrorNotification(
                        title: config('alert.notification_title', '🚨 Application Error'),
                        message: $e->getMessage(),
                        context: [
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'url' => request()->fullUrl(),
                            'user_id' => Auth::id(),
                            'environment' => app()->environment(),
                            'trace' => $e->getTraceAsString(),
                        ],
                    );

                    $notifiable = Notification::route('telegram', $chatId);

                    // Check if notifications should be queued
                    if (config('alert.queue', true)) {
                        $notifiable->notify($notification);
                    } else {
                        // Send synchronously - notifyNow bypasses the queue
                        $notifiable->notifyNow($notification);
                    }
                } catch (Throwable $notificationError) {
                    // Silently fail to prevent infinite loops
                    // Optionally log the notification error
                    if (config('alert.log_notification_errors', false)) {
                        logger()->error('Alert notification failed', [
                            'error' => $notificationError->getMessage(),
                        ]);
                    }
                }
            });
    }
}
