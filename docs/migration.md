# Migration Guide

## Upgrading to Version 6.3.0

Version 6.3.0 introduces breaking changes that require manual intervention.

### Breaking Changes

#### 1. Route Configuration Format Change

**Before (≤6.2.x):**
Routes were automatically loaded as YAML files.

**After (6.3.0+):**
Routes are now in PHP format and must be imported manually.

**Migration Steps:**

1. Create route configuration file:
```yaml
# config/routes/svc_profile.yaml
_svc_profile:
    resource: '@SvcProfileBundle/config/routes.php'
    prefix: /profile  # Adjust prefix as needed
```

2. If you used translations with locale routing:
```yaml
# config/routes/svc_profile.yaml
_svc_profile:
    resource: '@SvcProfileBundle/config/routes.php'
    prefix: /profile/{_locale}
    requirements: {"_locale": "%app.supported_locales%"}
```

#### 2. Security Configuration (Now Required)

The bundle now requires a secret key for token generation.

**Add to your .env file:**
```sh
###> svc/profile-bundle ###
SVC_PROFILE_HASH_SECRET=YOUR_GENERATED_SHA256_KEY_HERE
###< svc/profile-bundle ###
```

**Generate a secure key using:**
- https://passwordsgenerator.net/sha256-hash-generator
- `openssl rand -hex 32`
- `php -r "echo hash('sha256', random_bytes(32));"`

### Security Improvements in 6.3.0

- **XSS Vulnerability Fixed**: URL parameters are now properly validated
- **Enhanced Email Validation**: Blocks disposable email domains
- **Token Format Validation**: Strict validation of activation tokens
- **Input Sanitization**: All user input is properly sanitized

### Testing Your Migration

After upgrading, test the following:

1. **Route Access**: Verify routes are accessible at their expected URLs
2. **Email Change Flow**: Test the complete email change process
3. **Password Change Flow**: Test the password change functionality
4. **Token Security**: Ensure activation emails work correctly

### Rollback Plan

If you encounter issues, you can:

1. **Downgrade to 6.2.x**: `composer require svc/profile-bundle:^6.2`
2. **Remove manual route configuration** if you downgrade
3. **Remove environment variable** if you downgrade (optional)

## Upgrading from Version 5.x

If upgrading from version 5.x:

1. Update PHP requirement to ≥8.2
2. Update Symfony requirement to ≥6.4
3. Follow the 6.3.0 migration steps above
4. The `symfony/ux-toggle-password` package is now required

## Troubleshooting

### Common Issues

**Route not found errors:**
- Ensure you've created the route configuration file
- Check the route prefix matches your expectations
- Clear cache: `php bin/console cache:clear`

**Token errors:**
- Verify `SVC_PROFILE_HASH_SECRET` is set in your environment
- Ensure the secret key is exactly 64 hexadecimal characters
- Check that your web server can access environment variables

**Email validation errors:**
- The bundle now blocks disposable email domains
- Some previously valid emails might be rejected
- You can customize the disposable domain list by extending the validator

### Getting Help

If you encounter issues:

1. Check this migration guide
2. Review the [Usage documentation](usage.md)
3. Open an issue on GitHub with:
   - Your version numbers (bundle, Symfony, PHP)
   - Error messages
   - Configuration files