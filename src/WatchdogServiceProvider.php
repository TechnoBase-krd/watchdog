<?php

namespace Technobase\Watchdog;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use Technobase\Watchdog\Notifications\TelegramErrorNotification;
use Throwable;

class WatchdogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/watchdog.php',
            'watchdog'
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
                __DIR__ . '/../config/watchdog.php' => config_path('watchdog.php'),
            ], 'watchdog-config');
        }

        // Register exception handler if enabled
        if (config('watchdog.enabled', true)) {
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
                $enabledEnvironments = config('watchdog.enabled_environments', ['production', 'staging']);

                if (!in_array(app()->environment(), $enabledEnvironments)) {
                    return;
                }

                // Get chat ID from config or environment
                $chatId = config('watchdog.chat_id') ?? env('TELEGRAM_CHAT_ID');

                if (empty($chatId)) {
                    return;
                }

                try {
                    Notification::route('telegram', $chatId)
                        ->notify(new TelegramErrorNotification(
                            title: config('watchdog.notification_title', '🚨 Application Error'),
                            message: $e->getMessage(),
                            context: [
                                'file' => $e->getFile(),
                                'line' => $e->getLine(),
                                'url' => request()?->fullUrl(),
                                'user_id' => Auth::id(),
                                'environment' => app()->environment(),
                                'trace' => $e->getTraceAsString(),
                            ],
                        ));
                } catch (Throwable $notificationError) {
                    // Silently fail to prevent infinite loops
                    // Optionally log the notification error
                    if (config('watchdog.log_notification_errors', false)) {
                        logger()->error('Watchdog notification failed', [
                            'error' => $notificationError->getMessage(),
                        ]);
                    }
                }
            });
    }
}
