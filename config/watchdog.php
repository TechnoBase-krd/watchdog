<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Watchdog Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether Watchdog error notifications are enabled.
    | Set to false to completely disable the package functionality.
    |
    */

    'enabled' => env('WATCHDOG_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    |
    | Your Telegram Bot API token. Get it from @BotFather on Telegram.
    | This is used to authenticate requests to the Telegram API.
    |
    */

    'bot_token' => env('TELEGRAM_BOT_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Telegram Chat ID
    |--------------------------------------------------------------------------
    |
    | The Telegram chat ID where error notifications will be sent.
    | This can be a channel (negative ID), group, or private chat.
    |
    */

    'chat_id' => env('TELEGRAM_CHAT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Enabled Environments
    |--------------------------------------------------------------------------
    |
    | Specify which environments should send error notifications to Telegram.
    | Common values: production, staging, local, testing
    |
    */

    'enabled_environments' => [
        'production',
        'staging',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Title
    |--------------------------------------------------------------------------
    |
    | The title/heading shown in error notifications.
    | You can customize this for your application.
    |
    */

    'notification_title' => env('WATCHDOG_NOTIFICATION_TITLE', '🚨 Application Error'),

    /*
    |--------------------------------------------------------------------------
    | Stack Trace Lines
    |--------------------------------------------------------------------------
    |
    | Maximum number of stack trace lines to include in notifications.
    | Set to 0 to disable stack trace, or higher number for more detail.
    |
    */

    'trace_lines' => env('WATCHDOG_TRACE_LINES', 10),

    /*
    |--------------------------------------------------------------------------
    | Queue Notifications
    |--------------------------------------------------------------------------
    |
    | Whether to queue error notifications for async processing.
    | Recommended to keep true to avoid blocking the application.
    |
    */

    'queue' => env('WATCHDOG_QUEUE', true),

    /*
    |--------------------------------------------------------------------------
    | Queue Connection
    |--------------------------------------------------------------------------
    |
    | The queue connection to use for sending notifications.
    | Defaults to your application's default queue connection.
    |
    */

    'queue_connection' => env('WATCHDOG_QUEUE_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | Log Notification Errors
    |--------------------------------------------------------------------------
    |
    | Whether to log errors that occur when sending Telegram notifications.
    | Useful for debugging but can be disabled in production.
    |
    */

    'log_notification_errors' => env('WATCHDOG_LOG_ERRORS', false),

    /*
    |--------------------------------------------------------------------------
    | Include Request Data
    |--------------------------------------------------------------------------
    |
    | Whether to include request data (URL, user ID) in notifications.
    | Disable if you want minimal error information only.
    |
    */

    'include_request_data' => env('WATCHDOG_INCLUDE_REQUEST', true),

    /*
    |--------------------------------------------------------------------------
    | Include Environment Info
    |--------------------------------------------------------------------------
    |
    | Whether to include environment name in error notifications.
    | Useful to distinguish between production and staging errors.
    |
    */

    'include_environment' => env('WATCHDOG_INCLUDE_ENV', true),

];
