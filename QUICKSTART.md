# Watchdog - Quick Start Guide

Get up and running with Watchdog in 5 minutes!

## Step 1: Install Package

```bash
composer require technobase/watchdog
```

## Step 2: Create Telegram Bot

1. Open Telegram and message [@BotFather](https://t.me/BotFather)
2. Send `/newbot` command
3. Follow prompts to name your bot
4. Copy the bot token (looks like: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`)

## Step 3: Create Telegram Channel

1. Create a new channel in Telegram
2. Add your bot as an administrator (with "Post Messages" permission)
3. Get the channel ID:
   - Forward a message from your channel to [@userinfobot](https://t.me/userinfobot)
   - It will reply with the channel ID (negative number like `-1001234567890`)

## Step 4: Configure Environment

Add to your `.env` file:

```env
TELEGRAM_BOT_TOKEN=your-bot-token-here
TELEGRAM_CHAT_ID=your-channel-id-here
```

## Step 5: Publish Config (Optional)

```bash
php artisan vendor:publish --tag=watchdog-config
```

This creates `config/watchdog.php` for advanced configuration.

## Step 6: Test Integration

Create a test route in `routes/web.php` or `routes/api.php`:

```php
use Illuminate\Support\Facades\Notification;
use Technobase\Watchdog\Notifications\TestTelegramNotification;

Route::get('/test-watchdog', function() {
    Notification::route('telegram', config('watchdog.chat_id'))
        ->notify(new TestTelegramNotification());
    
    return 'Check your Telegram channel!';
});
```

Visit `http://your-app.test/test-watchdog` and check your Telegram channel for a test message.

## Step 7: Test Error Notifications

Create an error test route:

```php
Route::get('/test-error', function() {
    throw new \Exception('Test error - Watchdog should catch this!');
});
```

Make sure your `APP_ENV` is set to `production` or `staging` in `.env`, then visit the route. You should receive an error notification in Telegram!

## That's It! 🎉

Watchdog is now monitoring your application. All exceptions in production/staging will be sent to your Telegram channel automatically.

## Next Steps

- **Customize notifications**: Edit `config/watchdog.php`
- **Multiple environments**: Create separate channels for production and staging
- **Queue worker**: Make sure your queue worker is running for async notifications
- **Read full docs**: Check [README.md](README.md) for all features

## Troubleshooting

### Not receiving notifications?

1. ✅ Check bot is admin in channel
2. ✅ Verify `TELEGRAM_BOT_TOKEN` and `TELEGRAM_CHAT_ID` in `.env`
3. ✅ Ensure `APP_ENV=production` or `staging`
4. ✅ Run `php artisan config:cache` after `.env` changes
5. ✅ Check queue worker is running: `php artisan queue:work`

### Test with curl:

```bash
curl -X POST "https://api.telegram.org/botYOUR_TOKEN/sendMessage" \
  -d "chat_id=YOUR_CHAT_ID" \
  -d "text=Manual test"
```

If this works but Laravel doesn't, check Laravel logs in `storage/logs/laravel.log`.

## Support

Need help? Contact [dev@technobase.com](mailto:dev@technobase.com) or open an issue on GitHub.
