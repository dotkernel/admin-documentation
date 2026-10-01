# Upgrading from 7.0 to 7.2

## Summary

This page lists the pull requests that make up the 7.0 to 7.2 upgrade of Dotkernel Admin.
They come from the 7.1.0 and 7.2.0 releases and are split into important and optional updates, grouped by release.
Apply them in release order.

## Details

> You can find a complete list in [Changelog](https://github.com/dotkernel/admin/blob/7.0/CHANGELOG.md)

### Important updates

Important updates affect a project that has copied the Dotkernel Admin skeleton.

#### 7.1.0

* Bump `dotkernel/dot-maker` to version `2.x`, adds a `conflict` entry that blocks versions below 2.0.
  It is dev-only and matters when you use `composer make` https://github.com/dotkernel/admin/pull/406
* Add PHP `8.5` support, the PHP constraint also accepts 8.5 and `IpService::getUserIp()` falls back to `null` when `REMOTE_ADDR` is not set.
  Read it together with the 7.2.0 pull request that removes PHP 8.2 https://github.com/dotkernel/admin/pull/414

#### 7.2.0

* Update robots.txt, the file changes from the invalid text `deny all` to `User-agent: *` and `Disallow: /`, which blocks every crawler.
  A project that serves its own `robots.txt` can keep it https://github.com/dotkernel/admin/pull/416
* Browscap mapping, this pull request changes the `admin_login` table.
  The columns `deviceBrand`, `deviceModel`, `osPlatform`, `clientEngine` and `clientVersion` are removed and the column `isCrawler` is added.
  `AdminLoginService` fills the device and client fields with `get_browser()`, and only when the PHP `browscap` ini setting is set.
  The `AdminLogin` entity loses the getters and setters of the removed columns and gains `getIsCrawler()` and `setIsCrawler()`.
  The login list template drops five columns and gains an `Is Crawler` column.
  The pull request contains no migration, read the FAQ before applying it https://github.com/dotkernel/admin/pull/418
* Bump phpunit to v12.5.23, which also removes PHP 8.2 support, so the PHP constraint is now `~8.3.0 || ~8.4.0 || ~8.5.0`.
  `phpunit.xml` gains three `displayDetailsOn...` attributes and the unit tests use stubs where no expectations are configured.
  Read it together with the 7.1.0 pull request that adds PHP 8.5 https://github.com/dotkernel/admin/pull/420

### Optional updates

Skipping an optional update does not change how the application runs.

#### 7.1.0

* Update Qodana action version to v2025.3 https://github.com/dotkernel/admin/pull/411

#### 7.2.0

* No optional updates in this release.

## FAQ

**Q: Where can I find the complete list of changes for the 7.2 upgrade?**

A: In the [Changelog](https://github.com/dotkernel/admin/blob/7.0/CHANGELOG.md).

**Q: Which PHP versions does 7.2 support?**

A: The PHP constraint is `~8.3.0 || ~8.4.0 || ~8.5.0`.
PHP 8.5 was added in 7.1.0 and PHP 8.2 was removed in 7.2.0, so a project on PHP 8.2 must upgrade PHP before applying the 7.2.0 updates.

**Q: Do I need a database migration?**

A: Yes, because of the `admin_login` changes.
The pull request does not include a migration, so generate one with `vendor/bin/doctrine-migrations diff` and review it before running it.
It drops the columns `deviceBrand`, `deviceModel`, `osPlatform`, `clientEngine` and `clientVersion` and adds the column `isCrawler`.
Dropping the columns discards the device and client data stored in them, so back up the `admin_login` table first.

**Q: What is the `browscap` ini setting for?**

A: `AdminLoginService` reads the browser and device information of a login from `get_browser()`, which needs the `browscap` ini setting in your PHP configuration.
Without it, the device and client fields are saved empty, and `isMobile` and `isCrawler` are saved as `no`.

**Q: Do I need to update my tests for PHPUnit 12?**

A: Only if your project copied the Dotkernel Admin tests.
Mock objects without configured expectations become stubs, and `with()` is no longer used on stubs.
Check the changes of the pull request on the test files you copied.

**Q: Do I need to apply the `robots.txt` change?**

A: Only if you deploy the `public/robots.txt` file of the skeleton.
It now blocks all crawlers with valid directives.

**Q: Are the optional updates required?**

A: No, they are CI changes and do not change how the application runs.

**Q: In what order do I apply the pull requests?**

A: In release order: 7.1.0 first, then 7.2.0.
Read pull requests 414 and 420 together, because the first adds PHP 8.5 and the second removes PHP 8.2.
