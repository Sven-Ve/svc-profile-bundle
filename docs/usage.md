Usage
=====

* adapt the default url prefix in config/routes/svc_profile.yaml

```yaml
# /config/routes/svc_profile.yaml
_svc_profile:
    resource: '@SvcProfileBundle/src/Resources/config/routes.xml'
    prefix: /profile/svc
```
    
* integrate the change mail controller via path "svc_profile_change_mail_start"
* integrate the change password controller via path "svc_profile_change_pw_start"

* enable captcha (if installed and configured), default = false

```yaml
# /config/packages/svc_profile.yaml
svc_profile:
    # Enable captcha for change email/password forms?
    enableCaptcha: true
```
