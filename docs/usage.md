Usage
=====

* adapt the default url prefix in config/routes/svc_profile.yaml

```yaml
_svc_profile:
    resource: '@SvcProfileBundle/src/Resources/config/routes.xml'
    prefix: /profile/svc
```
    
* integrate the main controller via path svc_profile_change_mail_start
