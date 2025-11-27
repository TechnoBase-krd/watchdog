# Testing with Pest

This package uses Pest for testing. Pest provides an elegant and expressive testing syntax.

## Installation

First, install Pest and its dependencies:

```bash
composer install
```

Or if you need to add Pest separately:

```bash
composer require pestphp/pest --dev --with-all-dependencies
composer require pestphp/pest-plugin-laravel --dev
```

## Running Tests

Run all tests:

```bash
./vendor/bin/pest
```

Run tests with coverage:

```bash
./vendor/bin/pest --coverage
```

Run specific test file:

```bash
./vendor/bin/pest tests/Feature/NotificationTest.php
```

Run tests in parallel:

```bash
./vendor/bin/pest --parallel
```

Run with detailed output:

```bash
./vendor/bin/pest --verbose
```

## Test Structure

- **Feature Tests** (`tests/Feature/`): Test full features and integrations

  - `NotificationTest.php` - Tests for notification sending and behavior
  - `ExceptionHandlerTest.php` - Tests for exception handling integration
  - `ConfigurationTest.php` - Tests for configuration values

- **Unit Tests** (`tests/Unit/`): Test individual components
  - `ServiceProviderTest.php` - Tests for service provider registration
  - `NotificationPropertiesTest.php` - Tests for notification properties and traits

## Test Coverage

The test suite covers:

### Notification Tests

- ✅ Sending test notifications
- ✅ Telegram channel configuration
- ✅ Error notification structure
- ✅ Context information inclusion
- ✅ Queue configuration (enabled/disabled)
- ✅ Custom queue connections
- ✅ Sync connection when queue disabled

### Exception Handler Tests

- ✅ Exception reporting to Telegram
- ✅ Respecting enabled/disabled state
- ✅ Environment filtering
- ✅ Chat ID requirement
- ✅ Context inclusion in notifications

### Configuration Tests

- ✅ Default configuration values
- ✅ Configuration overrides
- ✅ Queue settings
- ✅ Trace line limits
- ✅ Notification titles
- ✅ Environment filtering
- ✅ Request data inclusion
- ✅ Environment info inclusion

### Service Provider Tests

- ✅ Provider registration
- ✅ Config publishing
- ✅ Config loading

### Notification Property Tests

- ✅ Retry configuration
- ✅ Timeout settings
- ✅ ShouldQueue implementation
- ✅ Queueable trait usage

## Writing New Tests

Pest uses a simple, expressive syntax:

```php
test('description of what is being tested', function () {
    // Arrange
    $notification = new TestTelegramNotification('message');

    // Act & Assert
    expect($notification->via(new stdClass()))->toContain('telegram');
});
```

### Using Expectations

```php
// Check equality
expect($value)->toBe('expected');

// Check type
expect($value)->toBeInstanceOf(SomeClass::class);

// Check array contents
expect($array)->toContain('item');

// Chain expectations
expect($config)
    ->toBeArray()
    ->toHaveCount(3)
    ->toContain('value');
```

### Using beforeEach and afterEach

```php
beforeEach(function () {
    config(['alert.enabled' => true]);
});

test('something', function () {
    // Config is already set
});
```

## Debugging Tests

Run a single test:

```bash
./vendor/bin/pest --filter="can send test telegram notification"
```

Stop on first failure:

```bash
./vendor/bin/pest --stop-on-failure
```

Show detailed errors:

```bash
./vendor/bin/pest -vvv
```

## Continuous Integration

Add to your CI pipeline:

```yaml
# .github/workflows/tests.yml
- name: Run tests
  run: ./vendor/bin/pest --ci
```
