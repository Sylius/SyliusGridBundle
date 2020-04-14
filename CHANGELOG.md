# CHANGELOG

## CHANGELOG FOR `1.7.x`

### v1.7.5 (2020-04-14)

- [#33](https://github.com/Sylius/SyliusGridBundle/issues/33) Replace deprecated doctrine object manager ()
- [#31](https://github.com/Sylius/SyliusGridBundle/issues/31) Allow twig 3.x ()
- [#32](https://github.com/Sylius/SyliusGridBundle/issues/32) Remove deprecated templating.helper configuration ()
- [#36](https://github.com/Sylius/SyliusGridBundle/issues/36) Add doctrine/persistence to require dev ()
- [#30](https://github.com/Sylius/SyliusGridBundle/issues/30) Add support for php 7.4 and symfony 4.4 ()
- [#34](https://github.com/Sylius/SyliusGridBundle/issues/34) Add support for symfony 5 and remove support for < 4.4 ()
- [#47](https://github.com/Sylius/SyliusGridBundle/issues/47) Fix build ([@loic425](https://github.com/loic425))
- [#49](https://github.com/Sylius/SyliusGridBundle/issues/49) Add multiple values support in SelectFilter ([@macintoshplus](https://github.com/macintoshplus))
- [#53](https://github.com/Sylius/SyliusGridBundle/issues/53) Fix the build by removing docblock and adding contracts event dispatcher to psalm config ([@GSadee](https://github.com/GSadee))
- [#54](https://github.com/Sylius/SyliusGridBundle/issues/54) Add possibility to configure custom service for query builder in doctrine orm driver ([@GSadee](https://github.com/GSadee))
- [#55](https://github.com/Sylius/SyliusGridBundle/issues/55) Revert "Fix autojoining with multiple aliases" ([@pamil](https://github.com/pamil))

### v1.7.4 (2020-01-02)

- [#24](https://github.com/Sylius/SyliusGridBundle/issues/24) fix cs issues ([@vvasiloi](https://github.com/vvasiloi))
- [#25](https://github.com/Sylius/SyliusGridBundle/issues/25) Process all grid filter, field and driver service tags ([@vvasiloi](https://github.com/vvasiloi))
- [#26](https://github.com/Sylius/SyliusGridBundle/issues/26) Fix filtering entities with multiple values ([@loic425](https://github.com/loic425))
- [#35](https://github.com/Sylius/SyliusGridBundle/issues/35) Fix autojoining with multiple aliases ([@pamil](https://github.com/pamil))

### v1.7.3 (2019-10-18)

- [#21](https://github.com/Sylius/SyliusGridBundle/issues/21) Fix getting metadata for custom JOINs on not-mapped associations ([@pamil](https://github.com/pamil))

### v1.7.2 (2019-10-17)

- [#19](https://github.com/Sylius/SyliusGridBundle/issues/19) Fix getting absolute path for field being already joined ([@pamil](https://github.com/pamil))

### v1.7.1 (2019-10-17)

- [#18](https://github.com/Sylius/SyliusGridBundle/issues/18) Prohibit association alias from starting with a number ([@pamil](https://github.com/pamil))

### v1.7.0 (2019-10-17)

- [#17](https://github.com/Sylius/SyliusGridBundle/issues/17) Add embeddable support ([@pamil](https://github.com/pamil))

## CHANGELOG FOR `1.6.x`

### v1.6.3 (2019-10-10)

- [#16](https://github.com/Sylius/SyliusGridBundle/issues/16) Support for Symfony 3.4 / 4.3+ ([@pamil](https://github.com/pamil))

### v1.6.2 (2019-10-10)

- [#8](https://github.com/Sylius/SyliusGridBundle/issues/8) Use expression type from options if set ([@vvasiloi](https://github.com/vvasiloi))
- [#15](https://github.com/Sylius/SyliusGridBundle/issues/15) Introduce Psalm ([@pamil](https://github.com/pamil))

### v1.6.1 (2019-08-28)

- [#13](https://github.com/Sylius/SyliusGridBundle/issues/13) [HotFix] Fixed joins for declared declared relations ([@lchrusciel](https://github.com/lchrusciel))
- [#14](https://github.com/Sylius/SyliusGridBundle/issues/14) Add a test for the hotfix ([@pamil](https://github.com/pamil))

### v1.6.0 (2019-08-28)

- [#3](https://github.com/Sylius/SyliusGridBundle/issues/3) [ORM] Refactor expresssion builder to add joins when needed ([@GSadee](https://github.com/GSadee))
- [#6](https://github.com/Sylius/SyliusGridBundle/issues/6) Fix default datetime format ([@Zales0123](https://github.com/Zales0123))
- [#7](https://github.com/Sylius/SyliusGridBundle/issues/7) Fix the build by creating better tests ([@pamil](https://github.com/pamil))
- [#10](https://github.com/Sylius/SyliusGridBundle/issues/10) README Symfony version fix ([@Zales0123](https://github.com/Zales0123))
- [#11](https://github.com/Sylius/SyliusGridBundle/issues/11) Add functional tests for GridBundle ([@pamil](https://github.com/pamil))
- [#12](https://github.com/Sylius/SyliusGridBundle/issues/12) Improve tests for sorting ([@pamil](https://github.com/pamil))


## CHANGELOG FOR `1.5.x`

### v1.5.1 (2019-05-21)

#### TL;DR

**Security release!**

[CVE-2019-12186](https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2019-12186): XSS vulnerability in string field type when rendering objects implementing `__toString` method returning injected code.

### v1.5.0 (2019-05-01)

#### TL;DR

- Released GridBundle as a standalone package, containing a subtree split of Grid component.

## CHANGELOG FOR `1.4.x`

### v1.4.5 (2019-05-21)

#### TL;DR

**Security release!**

[CVE-2019-12186](https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2019-12186): XSS vulnerability in string field type when rendering objects implementing `__toString` method returning injected code.

### v1.4.4 (2019-04-30)

#### TL;DR

- Released GridBundle as a standalone package, containing a subtree split of Grid component.

## CHANGELOG FOR `1.3.x`

### v1.3.13 (2019-05-21)

#### TL;DR

**Security release!**

[CVE-2019-12186](https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2019-12186): XSS vulnerability in string field type when rendering objects implementing `__toString` method returning injected code.

### v1.3.12 (2019-04-30)

#### TL;DR

- Released GridBundle as a standalone package, containing a subtree split of Grid component.


## CHANGELOG FOR `1.2.x`

### v1.2.18 (2019-05-21)

#### TL;DR

**Security release!**

[CVE-2019-12186](https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2019-12186): XSS vulnerability in string field type when rendering objects implementing `__toString` method returning injected code.

### v1.2.17 (2019-04-30)

#### TL;DR

Released GridBundle as a standalone package, containing a subtree split of Grid component.


## CHANGELOG FOR `1.1.x`

### v1.1.19 (2019-05-21)

#### TL;DR

**Security release!**

[CVE-2019-12186](https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2019-12186): XSS vulnerability in string field type when rendering objects implementing `__toString` method returning injected code.

### v1.1.18 (2019-04-30)

#### TL;DR

Released GridBundle as a standalone package, containing a subtree split of Grid component.
