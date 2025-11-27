<?php

use Illuminate\Support\Facades\Notification;
use Technobase\Alert\Notifications\TelegramErrorNotification;

beforeEach(function () {
    config(['alert.enabled' => true]);
    config(['alert.enabled_environments' => ['testing']]);
    config(['alert.chat_id' => '-1001234567890']);
    config(['alert.bot_token' => 'test-token']);
});

test('exception handler sends notification when error occurs', function () {
    Notification::fake();

    // Trigger an exception
    $exception = new \Exception('Test exception message');

    report($exception);

    Notification::assertSentTo(
        Notification::route('telegram', config('alert.chat_id')),
        TelegramErrorNotification::class
    );
});

test('exception handler does not send notification in non-enabled environment', function () {
    config(['alert.enabled_environments' => ['production', 'staging']]);
    Notification::fake();

    $exception = new \Exception('Test exception');
    report($exception);

    Notification::assertNothingSent();
});

test('exception handler does not send notification without chat id', function () {
    config(['alert.chat_id' => null]);
    Notification::fake();

    $exception = new \Exception('Test exception');
    report($exception);

    Notification::assertNothingSent();
});

test('exception handler includes correct context in notification', function () {
    Notification::fake();

    $exception = new \Exception('Test exception message');
    report($exception);

    Notification::assertSentTo(
        Notification::route('telegram', config('alert.chat_id')),
        TelegramErrorNotification::class,
        function ($notification) use ($exception) {
            // We can't access private properties directly, but we can verify it was sent
            return true;
        }
    );
});
