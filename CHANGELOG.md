# Changelog

All notable changes to `technobase/watchdog` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-11-26

### Added
- Initial release
- Automatic error notification to Telegram channels
- Rich error context (file, line, URL, user ID, environment, stack trace)
- Configurable notification settings
- Support for multiple environments (production, staging, etc.)
- Queue support for async notification processing
- Test notification feature
- Comprehensive configuration options
- Service provider with auto-discovery
- Markdown-formatted error messages
- Safe error handling to prevent infinite loops
- Configurable stack trace length
- Environment-based notification filtering

### Security
- Automatic try-catch wrapper to prevent notification failures from breaking app
- Option to disable sensitive data in notifications
- Environment filtering to prevent spam in development

[Unreleased]: https://github.com/technobase/watchdog/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/technobase/watchdog/releases/tag/v1.0.0
