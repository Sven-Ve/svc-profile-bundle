# CHANGELOG

## Version 1.0.0
- added pasword change controller
- added captcha to change profile/password

## Version 1.1.0
- autofocus for first field in change password/mail forms
- allow external storage for hash secret key
- remove extending ChangeMailHelper from AbstractController
- request php >7.4.0 or >8.0.0

## Version 1.2.0
- Translation to DE

## Version 1.3.0
- require svc-utilbundle >= 1.0
- added tests

## Version v1.3.1
*Fri, 11 Jun 2021 10:15:22 +0000*
- move recipe to private flex server


## Version v1.3.1
*Fri, 11 Jun 2021 10:15:45 +0000*
- move recipe to private flex server


## Version v1.3.2
*Fri, 11 Jun 2021 10:16:03 +0000*
- move recipe to private flex server


## Version v1.4.0
*Tue, 29 Jun 2021 11:26:37 +0000*
- first public, open source version


## Version v1.4.1
*Tue, 29 Jun 2021 11:54:37 +0000*
- added badges, deployed to Packagist


## Version v1.4.2
*Mon, 19 Jul 2021 19:59:04 +0000*
- moved to symfony 5.3 authentication system for password change


## Version v1.4.3
*Wed, 04 Aug 2021 15:06:43 +0000*
- added static code analysis (phpstan)


## Version v1.5.0
*Thu, 31 Mar 2022 20:50:36 +0000*
- ready for symfony 5.4 and SvcUtilBundle >2.x


## Version v1.5.1
*Fri, 22 Apr 2022 20:11:18 +0000*
- fixes for symfony 5.4


## Version v1.6.0
*Sat, 23 Apr 2022 19:34:42 +0000*
- compatible with symfony 6.0


## Version v1.6.1
*Wed, 27 Apr 2022 16:21:18 +0000*
- compatible with symfony 6.0 (fix)


## Version 3.0.0
*Sat, 30 Apr 2022 20:29:08 +0000*
- compatible with symfony 6.0 (fix)


## Version 3.0.1
*Sat, 14 May 2022 16:44:09 +0000*
- added php attribute types


## Version 3.0.2
*Sat, 14 May 2022 20:19:04 +0000*
- fix test script


## Version 3.0.3
*Sat, 14 May 2022 20:29:12 +0000*
- fix inconsistent type for expireAt (new DateTimeImmutable)


## Version 3.0.4
*Sat, 14 May 2022 20:42:55 +0000*
- fix phpstan error


## Version 3.0.5
*Sat, 14 May 2022 20:59:59 +0000*
- fix php deprecation


## Version 3.0.6
*Tue, 17 May 2022 20:20:09 +0000*
- doctrine/annotations dependency deleted


## Version 4.0.0
*Mon, 18 Jul 2022 15:23:30 +0000*
- build with Symfony 6.1 bundle features, runs only with symfony 6.1


## Version 4.0.1
*Mon, 18 Jul 2022 15:24:59 +0000*
- build with Symfony 6.1 bundle features, runs only with symfony 6.1 (cleanup)


## Version 4.0.2
*Thu, 21 Jul 2022 18:33:32 +0000*
- licence year update


## Version 5.0.0
*Sat, 16 Dec 2023 19:43:24 +0000*
- ready for symfony 6.4 and 7


## Version 5.0.1
*Sun, 17 Dec 2023 18:42:54 +0000*
- ready for symfony 6.4 and 7 - fixing test errors


## Version 5.1.0
*Wed, 03 Jan 2024 20:16:15 +0000*
- switching to karser/karser-recaptcha3-bundle


## Version 6.0.0
*Sat, 27 Jan 2024 20:43:35 +0000*
- using symfony/ux-toggle-password to display the password, needs symfony >=6.4


## Version 6.1.0
*Fri, 08 Mar 2024 20:43:45 +0000*
- runs with doctrin/orm ^3 too


## Version 6.2.0
*Mon, 08 Jul 2024 09:36:16 +0000*
- better testing kernel, phpstan now level 7, fixed phpstan errors


## Version 6.3.0
*Sun, 14 Sep 2025 13:04:09 +0000*
- BREAKING CHANGE: PHP route configuration, manual import required. 
- SECURITY FIXES: XSS vulnerability, hardcoded secret removed, enhanced email validation. 
  - See migration guide.


## Version 6.4.0
*Mon, 27 Oct 2025 18:17:08 +0000*
- Add strict types declaration across multiple files
- update composer.json to support newer doctrine versions.


## Version 6.5.0
*Wed, 29 Oct 2025 15:17:09 +0000*
- Remove Symfony UX TogglePassword dependency, use TogglePassword implementation from Svc\UtilBundle. Add tests


## Version 6.6.0
*Wed, 19 Nov 2025 15:33:42 +0000*
- Tested with svc-utilbundle 7.x too.
