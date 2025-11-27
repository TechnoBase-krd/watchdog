<?php

test('config has correct default values', function () {
    expect(config('alert.enabled'))->toBeTrue()
        ->and(config('alert.queue'))->toBeTrue()
        ->and(config('alert.trace_lines'))->toBe(10)
        ->and(config('alert.notification_title'))->toBe('🚨 Application Error')
        ->and(config('alert.enabled_environments'))->toBeArray()
        ->and(config('alert.enabled_environments'))->toContain('production', 'staging');
});

test('config can be overridden', function () {
    config(['alert.enabled' => false]);

    expect(config('alert.enabled'))->toBeFalse();
});

test('queue configuration can be set', function () {
    config(['alert.queue' => false]);

    expect(config('alert.queue'))->toBeFalse();
});

test('queue connection can be customized', function () {
    config(['alert.queue_connection' => 'redis']);

    expect(config('alert.queue_connection'))->toBe('redis');
});

test('trace lines can be configured', function () {
    config(['alert.trace_lines' => 5]);

    expect(config('alert.trace_lines'))->toBe(5);
});

test('notification title can be customized', function () {
    config(['alert.notification_title' => '⚠️ Custom Error']);

    expect(config('alert.notification_title'))->toBe('⚠️ Custom Error');
});

test('enabled environments can be configured', function () {
    config(['alert.enabled_environments' => ['production']]);

    expect(config('alert.enabled_environments'))
        ->toBeArray()
        ->toHaveCount(1)
        ->toContain('production');
});

test('log notification errors setting works', function () {
    config(['alert.log_notification_errors' => true]);

    expect(config('alert.log_notification_errors'))->toBeTrue();
});

test('include request data setting works', function () {
    config(['alert.include_request_data' => false]);

    expect(config('alert.include_request_data'))->toBeFalse();
});

test('include environment setting works', function () {
    config(['alert.include_environment' => false]);

    expect(config('alert.include_environment'))->toBeFalse();
});
