Usage
=====

## Route Configuration

Adapt the default URL prefix in config/routes/svc_profile.yaml and enable translation (if you like it):

```yaml
# /config/routes/svc_profile.yaml
_svc_profile:
    resource: '@SvcProfileBundle/config/routes.php'
    prefix: /svc-profile/{_locale}
    requirements: {"_locale": "%app.supported_locales%"}
```

## Security Configuration (Required)

Generate a SHA256 secret key (you can use https://passwordsgenerator.net/sha256-hash-generator for it) and store the key in .env (or better .env.local):

```sh
###> svc/profile-bundle ###
SVC_PROFILE_HASH_SECRET=D9E143E74FC3E5AE3ED5305043FC67030C43CCDA5060EA2FD464BB8C0CC2D65A
###< svc/profile-bundle ###
```

**Important**: Never use the default hardcoded secret in production. Always set the `SVC_PROFILE_HASH_SECRET` environment variable.

## Integration

* Integrate the change mail controller via route name: `svc_profile_change_mail_start`
* Integrate the change password controller via route name: `svc_profile_change_pw_start`

Example in Twig template:
```twig
<a href="{{ path('svc_profile_change_mail_start') }}">{{ 'Change Email'|trans }}</a>
<a href="{{ path('svc_profile_change_pw_start') }}">{{ 'Change Password'|trans }}</a>
```

## Optional: reCAPTCHA v3 Protection

Enable reCAPTCHA v3 (if package "karser/karser-recaptcha3-bundle" is installed and configured), default = false:

```yaml
# /config/packages/svc_profile.yaml
svc_profile:
    # Enable captcha for change email/password forms?
    enableCaptcha: true
```

## Email Validation

The bundle includes comprehensive email validation:
- Format validation (RFC compliant)
- Disposable email domain blocking
- Domain MX record verification (when available)
- Maximum length validation (254 characters per RFC 5321)

## Security Features

- **XSS Protection**: All user input is validated and sanitized
- **Token Validation**: Secure token format validation (32 hex characters)
- **Password Requirements**: Minimum 8 characters (can be extended with custom validation)
- **CSRF Protection**: Built-in Symfony CSRF protection on all forms
