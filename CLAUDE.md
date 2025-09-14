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

### Core Components

**Controllers:**
- `ChangeMailController` - Handles email change requests and confirmations
- `ChangePWController` - Handles password change functionality

**Forms:**
- `ChangeMailType` - Email change form with validation
- `ChangePWType` - Password change form with validation

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
- Uses custom `SvcProfileKernel` test kernel
- UserDummy class for test data
- Functional tests for controllers and services
- PHPUnit configuration in `phpunit.xml.dist`

## Code Style
- Uses PHP-CS-Fixer with Symfony and PSR-12 standards
- Custom header comment with copyright information
- Short array syntax enforced
- Single quotes preferred
- Specific formatting rules for arrays and concatenation