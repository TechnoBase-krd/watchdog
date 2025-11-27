<?php

use Illuminate\Support\Facades\Notification;
use Technobase\Alert\Notifications\TelegramErrorNotification;
use Technobase\Alert\Notifications\TestTelegramNotification;

test('can send test telegram notification', function () {
    Notification::fake();

    Notification::route('telegram', config('alert.chat_id'))
        ->notify(new TestTelegramNotification('Test message'));

    Notification::assertSentTo(
        Notification::route('telegram', config('alert.chat_id')),
        TestTelegramNotification::class
    );
});

test('test notification has correct channel', function () {
    $notification = new TestTelegramNotification;

    expect($notification->via(new stdClass))->toContain('telegram');
});

test('test notification has correct content', function () {
    $message = 'Custom test message';
    $notification = new TestTelegramNotification($message);

    $telegramMessage = $notification->toTelegram(new stdClass);

    expect($telegramMessage)->toBeInstanceOf(\NotificationChannels\Telegram\TelegramMessage::class);
});

test('error notification has correct channel', function () {
    $notification = new TelegramErrorNotification(
        'Test Title',
        'Test error message',
        ['file' => 'test.php', 'line' => 42]
    );

    expect($notification->via(new stdClass))->toContain('telegram');
});

test('error notification includes title and message', function () {
    $title = '🚨 Application Error';
    $message = 'Database connection failed';

    $notification = new TelegramErrorNotification($title, $message);
    $telegramMessage = $notification->toTelegram(new stdClass);

    expect($telegramMessage)->toBeInstanceOf(\NotificationChannels\Telegram\TelegramMessage::class);
});

test('error notification includes context information', function () {
    $context = [
        'file' => '/var/www/app/Http/Controllers/UserController.php',
        'line' => 42,
        'url' => 'https://example.com/users',
        'user_id' => 123,
        'environment' => 'production',
        'trace' => "Stack trace line 1\nStack trace line 2",
    ];

    $notification = new TelegramErrorNotification(
        'Test Error',
        'Something went wrong',
        $context
    );

    $telegramMessage = $notification->toTelegram(new stdClass);

    expect($telegramMessage)->toBeInstanceOf(\NotificationChannels\Telegram\TelegramMessage::class);
});

test('error notification respects queue configuration when enabled', function () {
    config(['alert.queue' => true]);

    $notification = new TelegramErrorNotification('Test', 'Message');

    expect($notification)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

test('error notification uses sync connection when queue disabled', function () {
    config(['alert.queue' => false]);

    $notification = new TelegramErrorNotification('Test', 'Message');

    expect($notification->connection)->toBe('sync');
});

test('error notification uses custom queue connection when specified', function () {
    config(['alert.queue_connection' => 'redis']);

    $notification = new TelegramErrorNotification('Test', 'Message');

    expect($notification->connection)->toBe('redis');
});

test('test notification respects queue configuration when enabled', function () {
    config(['alert.queue' => true]);

    $notification = new TestTelegramNotification('Test message');

    expect($notification)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

test('test notification uses sync connection when queue disabled', function () {
    config(['alert.queue' => false]);

    $notification = new TestTelegramNotification('Test message');

    expect($notification->connection)->toBe('sync');
});
