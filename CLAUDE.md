# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is SvcProfileBundle, a Symfony bundle that provides user profile management features:
- Change user password (with email notification)
- Change user email address (with email confirmation)

The bundle follows modern Symfony architecture with:
- PHP 8.2+ requirement
- Symfony 6.4+ or 7.x support
- Doctrine ORM integration
- Optional reCAPTCHA support via configuration

## Development Commands

### Testing
- `composer run-script test` - Run PHPUnit tests with testdox output
- `vendor/bin/phpunit` - Run tests directly
- `vendor/bin/phpunit --testdox` - Run tests with descriptive output

### Code Quality
- `composer run-script phpstan` - Run PHPStan static analysis at level 7
- `/opt/homebrew/bin/php-cs-fixer fix` - Format code using PHP-CS-Fixer
- `php bin/release.php` - Run full release process (PHPStan + tests + git operations)

### PHPStan Configuration
- Level 7 analysis configured in `.phpstan.neon`
- Analyzes bin/, config/, src/, and tests/ directories
- Some specific errors are ignored for Symfony compatibility

## Architecture

### Bundle Structure
- `SvcProfileBundle` - Main bundle class with configuration support
- Configurable captcha enablement via `enableCaptcha` boolean option
- Service definitions in `config/services.php` (PHP format)
- Route definitions in `config/routes.php` (PHP format)
- **Breaking Change v6.3.0**: Routes must be imported manually in applications

### Security Architecture
- **XSS Protection**: All user input validated via Symfony Request objects
- **Token Security**: HMAC-SHA256 token hashing with configurable secret
- **Email Validation**: Custom ValidEmailDomain constraint blocks disposable domains
- **Environment Configuration**: Requires `SVC_PROFILE_HASH_SECRET` environment variable

### Core Components

**Controllers:**
- `ChangeMailController` - Handles email change requests and confirmations (XSS-safe)
- `ChangePWController` - Handles password change functionality

**Forms:**
- `ChangeMailType` - Email change form with comprehensive validation
- `ChangePWType` - Password change form with validation

**Validators:**
- `ValidEmailDomain` - Custom constraint for email domain validation
- `ValidEmailDomainValidator` - Blocks disposable emails and validates MX records

**Entity:**
- `UserChanges` - Tracks pending user changes (email/password)

**Services:**
- `ChangeMailHelper` - Business logic for email change process

**Repository:**
- `UserChangesRepository` - Data access for user changes

### Dependencies
- Extends AbstractBundle (modern Symfony bundle architecture)
- Requires svc/util-bundle for shared utilities
- Uses symfony/ux-toggle-password for password visibility toggle
- Optional karser/karser-recaptcha3-bundle integration

### Testing
- Uses custom `SvcProfileKernel` test kernel with `/profile/` route prefix
- UserDummy class for test data in `tests/Dummy/`
- PHPUnit configuration in `phpunit.xml.dist`
- **29 tests with 47 assertions** covering all critical functionality

#### Test Coverage by Component:

**Controllers (9 tests):**
- `ChangeMailControllerTest` - Email change functionality, XSS protection, token validation
- `ChangePWControllerTest` - Password change route accessibility

**Services (5 tests):**
- `ChangeMailHelperTest` - Token generation/hashing, expiration checks, email validation

**Validators (15 tests):**
- `ValidEmailDomainValidatorTest` - Disposable email detection, MX validation, edge cases

#### Security Testing:
- XSS attack prevention (validated in mail1Sent endpoint)
- SQL injection attempts in token validation
- Token format validation (32 hex characters required)
- Email validation with disposable domain blocking
- Case-insensitive domain checking

#### Testing Best Practices:
- All tests use PHPStan ignore comments for mock expectations
- Controller tests verify route accessibility (not 404) without full integration
- Validator tests use mocked contexts for isolated unit testing
- Security tests verify input sanitization and validation

### Configuration Flow
1. Bundle auto-configures services via `config/services.php`
2. Routes loaded from `config/routes.php` (manual import required in v6.3.0+)
3. Environment variable `SVC_PROFILE_HASH_SECRET` required for token security
4. Optional reCAPTCHA configuration via `svc_profile.enableCaptcha`

## Development Guidelines

### Security Requirements
- Always use Symfony Request objects instead of direct `$_GET`/`$_POST` access
- Validate all user input with appropriate constraints
- Use environment variables for secrets, never hardcode
- Test XSS prevention in templates and controllers

### Code Style
- Uses PHP-CS-Fixer with Symfony and PSR-12 standards
- Custom header comment with copyright information
- Short array syntax enforced
- Single quotes preferred
- Specific formatting rules for arrays and concatenation

### Localization
- German translations use "Du" form consistently
- All user-facing messages should be translatable via ProfileBundle domain

### Release Process
- CHANGELOG.md is automatically updated by `bin/release.php`
- Make version/message changes in `bin/release.php`, not CHANGELOG.md directly
- Release script runs PHPStan + tests before git operations