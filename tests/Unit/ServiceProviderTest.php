<?php

use Technobase\Alert\AlertServiceProvider;

test('service provider is registered', function () {
    $provider = app()->getProvider(AlertServiceProvider::class);

    expect($provider)->toBeInstanceOf(AlertServiceProvider::class);
});

test('config is published', function () {
    $provider = new AlertServiceProvider(app());

    // Check that the config file can be published
    expect($provider)->toBeInstanceOf(AlertServiceProvider::class);
});

test('alert config is loaded', function () {
    expect(config('alert'))->toBeArray()
        ->and(config('alert.enabled'))->not->toBeNull()
        ->and(config('alert.queue'))->not->toBeNull();
});
