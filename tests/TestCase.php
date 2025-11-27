<?php

namespace Technobase\Watchdog\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Technobase\Watchdog\WatchdogServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Get package providers.
     */
    protected function getPackageProviders($app): array
    {
        return [
            WatchdogServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Setup default configuration
        config()->set('watchdog.enabled', true);
        config()->set('watchdog.chat_id', '-1001234567890');
        config()->set('watchdog.bot_token', 'test-token');
        config()->set('watchdog.enabled_environments', ['production', 'staging']);
        config()->set('watchdog.trace_lines', 10);
    }
}
