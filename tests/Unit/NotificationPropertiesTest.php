<?php

use Technobase\Alert\Notifications\TelegramErrorNotification;
use Technobase\Alert\Notifications\TestTelegramNotification;

test('error notification has correct retry configuration', function () {
    $notification = new TelegramErrorNotification('Title', 'Message');

    expect($notification->tries)->toBe(3)
        ->and($notification->backoff)->toBe(60)
        ->and($notification->timeout)->toBe(30);
});

test('test notification has correct retry configuration', function () {
    $notification = new TestTelegramNotification;

    expect($notification->tries)->toBe(3)
        ->and($notification->backoff)->toBe(60)
        ->and($notification->timeout)->toBe(30);
});

test('error notification implements should queue', function () {
    $notification = new TelegramErrorNotification('Title', 'Message');

    expect($notification)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

test('test notification implements should queue', function () {
    $notification = new TestTelegramNotification;

    expect($notification)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

test('error notification uses queueable trait', function () {
    $notification = new TelegramErrorNotification('Title', 'Message');

    $traits = class_uses($notification);

    expect($traits)->toContain(\Illuminate\Bus\Queueable::class);
});

test('test notification uses queueable trait', function () {
    $notification = new TestTelegramNotification;

    $traits = class_uses($notification);

    expect($traits)->toContain(\Illuminate\Bus\Queueable::class);
});
