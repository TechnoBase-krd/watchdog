# Watchdog - Laravel Telegram Error Notifications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/technobase/watchdog.svg?style=flat-square)](https://packagist.org/packages/technobase/watchdog)
[![Total Downloads](https://img.shields.io/packagist/dt/technobase/watchdog.svg?style=flat-square)](https://packagist.org/packages/technobase/watchdog)
[![License](https://img.shields.io/packagist/l/technobase/watchdog.svg?style=flat-square)](https://packagist.org/packages/technobase/watchdog)

A Laravel package for sending comprehensive error notifications to Telegram channels with detailed context, stack traces, and environment information.

## Features

- 🚨 **Automatic Error Notifications** - Captures all exceptions and sends them to Telegram
- 📝 **Rich Error Context** - Includes file, line, URL, user ID, environment, and stack trace
- ⚙️ **Configurable** - Control which environments send notifications
- 🔒 **Safe** - Won't break your app if Telegram is unreachable
- 🎯 **Queue Support** - Async notification processing to avoid blocking requests
- 🧪 **Testable** - Includes test notification for verifying setup
- 🎨 **Markdown Formatting** - Clean, readable error messages in Telegram

## Requirements

- PHP 8.2 or higher
- Laravel 11.x or 12.x
- A Telegram Bot Token (get one from [@BotFather](https://t.me/BotFather))
- A Telegram Channel/Group/Chat ID

## Installation

Install the package via Composer:

```bash
composer require technobase/watchdog
```

The service provider will be automatically registered via Laravel's package discovery.

## Configuration

### 1. Publish Configuration File

```bash
php artisan vendor:publish --tag=watchdog-config
```

This creates `config/watchdog.php` in your application.

### 2. Set Environment Variables

Add these to your `.env` file:

```env
# Telegram Bot Token (from @BotFather)
TELEGRAM_BOT_TOKEN=your-bot-token-here

# Telegram Chat ID (channel, group, or private chat)
TELEGRAM_CHAT_ID=your-chat-id-here

# Optional: Customize settings
WATCHDOG_ENABLED=true
WATCHDOG_NOTIFICATION_TITLE="🚨 Application Error"
WATCHDOG_TRACE_LINES=10
WATCHDOG_QUEUE=true
```

### 3. Configure Telegram Bot

1. Create a bot by messaging [@BotFather](https://t.me/BotFather) on Telegram
2. Send `/newbot` and follow the prompts
3. Copy the bot token to your `.env` file
4. Create a Telegram channel for errors
5. Add your bot as an administrator to the channel
6. Get the channel ID (use [@userinfobot](https://t.me/userinfobot) or check Telegram API)
7. Add the chat ID to your `.env` file

## Usage

### Automatic Error Notifications

Once configured, Watchdog automatically catches all exceptions in production and staging environments and sends them to your Telegram channel.

No additional code needed! Just let your application run.

### Testing the Integration

Send a test notification to verify your setup:

```php
use Illuminate\Support\Facades\Notification;
use Technobase\Watchdog\Notifications\TestTelegramNotification;

Notification::route('telegram', config('watchdog.chat_id'))
    ->notify(new TestTelegramNotification('Testing Watchdog integration'));
```

Or create a test route:

```php
Route::get('/test-watchdog', function() {
    \Notification::route('telegram', config('watchdog.chat_id'))
        ->notify(new \Technobase\Watchdog\Notifications\TestTelegramNotification());
    
    return 'Test notification sent!';
});
```

### Manual Error Notifications

You can manually send error notifications:

```php
use Illuminate\Support\Facades\Notification;
use Technobase\Watchdog\Notifications\TelegramErrorNotification;

try {
    // Your code that might fail
    riskyOperation();
} catch (\Exception $e) {
    Notification::route('telegram', config('watchdog.chat_id'))
        ->notify(new TelegramErrorNotification(
            title: 'Custom Error Title',
            message: $e->getMessage(),
            context: [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'custom_data' => 'additional context',
            ]
        ));
    
    throw $e; // Re-throw if needed
}
```

### Disabling for Specific Environments

Edit `config/watchdog.php`:

```php
'enabled_environments' => [
    'production',
    'staging',
    // 'local' - not included, won't send notifications locally
],
```

Or disable entirely:

```env
WATCHDOG_ENABLED=false
```

## Configuration Options

| Option | Environment Variable | Default | Description |
|--------|---------------------|---------|-------------|
| `enabled` | `WATCHDOG_ENABLED` | `true` | Enable/disable package |
| `bot_token` | `TELEGRAM_BOT_TOKEN` | `null` | Telegram bot API token |
| `chat_id` | `TELEGRAM_CHAT_ID` | `null` | Telegram chat/channel ID |
| `enabled_environments` | - | `['production', 'staging']` | Environments to send notifications |
| `notification_title` | `WATCHDOG_NOTIFICATION_TITLE` | `🚨 Application Error` | Title of error notifications |
| `trace_lines` | `WATCHDOG_TRACE_LINES` | `10` | Max stack trace lines |
| `queue` | `WATCHDOG_QUEUE` | `true` | Queue notifications |
| `queue_connection` | `WATCHDOG_QUEUE_CONNECTION` | `null` | Queue connection to use |
| `log_notification_errors` | `WATCHDOG_LOG_ERRORS` | `false` | Log notification failures |
| `include_request_data` | `WATCHDOG_INCLUDE_REQUEST` | `true` | Include URL & user ID |
| `include_environment` | `WATCHDOG_INCLUDE_ENV` | `true` | Include environment name |

## Example Telegram Notification

```markdown
🚨 **Application Error**

**Message**: Call to undefined method User::nonExistent()

**File**: `/var/www/app/Services/UserService.php:42`
**URL**: `https://api.example.com/users/5`
**User**: `ID: 123`
**Environment**: `production`

**Trace**:
```
#0 UserController.php(23): UserService->process()
#1 Router.php(822): UserController->store()
#2 Pipeline.php(180): Router->dispatch()
...
```
```

## Troubleshooting

### Bot Not Receiving Messages

1. **Check bot is admin**: Your bot must be added as an administrator to the channel
2. **Verify chat ID**: Use negative ID for channels (e.g., `-1001234567890`)
3. **Test with curl**:
   ```bash
   curl -X POST "https://api.telegram.org/bot{YOUR_TOKEN}/sendMessage" \
     -d "chat_id={YOUR_CHAT_ID}" \
     -d "text=Test message"
   ```

### No Notifications in Production

1. **Check environment**: Verify `APP_ENV=production` or `staging`
2. **Check config**: Run `php artisan config:cache` after changes
3. **Check logs**: Enable `WATCHDOG_LOG_ERRORS=true` and check `storage/logs/laravel.log`
4. **Check queue**: If using queues, ensure queue worker is running: `php artisan queue:work`

### DNS Resolution Errors

If you see `Could not resolve host: api.telegram.org`:

1. Check internet connectivity
2. Flush DNS cache: `sudo dscacheutil -flushcache` (macOS)
3. Try different DNS servers (e.g., Google DNS 8.8.8.8)
4. Check firewall/antivirus settings

### Notifications Delayed

If notifications arrive late:

1. **Check queue worker**: `php artisan queue:work` must be running
2. **Disable queue**: Set `WATCHDOG_QUEUE=false` for immediate sending
3. **Check queue connection**: Verify your `QUEUE_CONNECTION` is working

## Security

### Sensitive Data

Be careful not to send sensitive information in error notifications:

- Avoid logging passwords, API keys, or tokens
- Consider filtering context data before sending
- Use environment-specific chat channels
- Review stack traces for sensitive information

### Best Practices

- Use separate Telegram channels for different environments
- Restrict channel access to authorized personnel only
- Regularly rotate bot tokens
- Monitor notification volume to detect attacks

## Testing

Run the package tests:

```bash
composer test
```

Or with PHPUnit directly:

```bash
vendor/bin/phpunit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## Contributing

Contributions are welcome! Please submit pull requests to the main repository.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Credits

- **Technobase** - Package development and maintenance
- [Laravel Notification Channels](https://github.com/laravel-notification-channels/telegram) - Telegram integration
- All contributors

## Support

For support, please contact [dev@technobase.com](mailto:dev@technobase.com) or open an issue on GitHub.

---

Made with ❤️ by [Technobase](https://technobase.com)
