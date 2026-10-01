# Upgrading from 6.0 to 7.0

## Summary

This page lists the pull requests that make up the 6.0 to 7.0 upgrade of Dotkernel Admin.
They come from the 6.1.0, 6.2.0 and 7.0.0 releases and are split into important and optional updates, grouped by release.
Apply them in release order.

## Details

> You can find a complete list in [Changelog](https://github.com/dotkernel/admin/blob/7.0/CHANGELOG.md)

### Important updates

Important updates affect a project that has copied the Dotkernel Admin skeleton.

#### 6.1.0

* Updated auth guards, first change of the login route rules. Read it together with the next pull request and with the 6.2.0 pull request that sets the final rules https://github.com/dotkernel/admin/pull/371
* Updated auth guards, sets the login route rules to an empty list. The final rules are set in 6.2.0 https://github.com/dotkernel/admin/pull/372
* Changed route names to be in line with API naming scheme, from `noun-verb` to `verb-noun`, for example `admin::admin-list` becomes `admin::list-admin`.
  URLs do not change, but the keys in `config/autoload/authorization-guards.global.php`, the `route_name` entries in `config/autoload/navigation.global.php`, the Twig template names and every `generateUri()` call must follow https://github.com/dotkernel/admin/pull/375
* Updated all handler names to match route names, for example `GetAdminListHandler` becomes `GetListAdminHandler`.
  Custom handlers, tests and `ConfigProvider` or `RoutesDelegator` entries that reference the old classes must be updated https://github.com/dotkernel/admin/pull/377
* Implemented `dotkernel/dot-maker` in dev mode, adds `dotkernel/dot-maker` to `require-dev`, the `make` Composer script and `process-timeout` set to `0` https://github.com/dotkernel/admin/pull/380
* Core sync, `OAuthAccessToken::$userId` gets a default value, only needed when your project has its own copy of the Core entity https://github.com/dotkernel/admin/pull/381

#### 6.2.0

* Removed the initial migration file from `src/Core/src/App/src/Migration`.
  If your database already ran it, keep your copy or remove it everywhere, otherwise it is reported as an unavailable migration https://github.com/dotkernel/admin/pull/383
* Added a comment with a new possible RBAC guard config.
  It also sets the login routes back to `unauthenticated`, which is the final state of the rules from the 6.1.0 pull requests.
  The commented wildcard config is optional and needs `dotkernel/dot-rbac-guard` `^3.6.1` or `^4.1.1` https://github.com/dotkernel/admin/pull/386
* Form updates, and the edit services no longer change the password when the submitted password is empty.
  The rest of the changes are cleanups https://github.com/dotkernel/admin/pull/388
* Removed the `mezzio/mezzio-tooling` dependency from `composer.json` and from `config/config.php` https://github.com/dotkernel/admin/pull/390
* Implemented Doctrine table prefixes through the new `table_prefix` key of the database configuration.
  The default is an empty prefix, so nothing changes unless you set one.
  Setting a prefix on an existing database requires renaming the tables yourself https://github.com/dotkernel/admin/pull/392
* Bump to PHP 8.4, the PHP constraint also accepts 8.4, `roave/psr-container-doctrine` also accepts `^6.0.0` and the CLI commands use `$defaultName` https://github.com/dotkernel/admin/pull/393

#### 7.0.0

* Core Sync and update codebase.
  This is the breaking pull request of the upgrade.
  The `uuid` identifier is renamed to `id` in entities, columns, route parameters, handlers and services, and `getUuid()` becomes `getId()`.
  The column type changes from `uuid_binary` to the native `uuid` type, so a migration is required and existing binary UUID data is not converted.
  `config/autoload/app.global.php` and `config/migrations.php` are deleted, and `local.php.dist`, `templates.global.php` and `cli-config.php` change.
  Read the FAQ before applying it https://github.com/dotkernel/admin/pull/403
* Core sync, `OAuthRefreshToken::setIdentifier()` no longer sets the id and `Message::restrictionDeprecation()` is removed.
  It only matters when you use the OAuth2 refresh token flow or call the removed method.
  Read it together with the previous pull request, which also changes `OAuthRefreshToken` https://github.com/dotkernel/admin/pull/398
* Bumped dependencies, raises the constraints of the `dotkernel/*`, `mezzio/*`, `laminas/*` and `ramsey/uuid` packages and adds `symfony/var-exporter` to `require` https://github.com/dotkernel/admin/pull/401

### Optional updates

Skipping an optional update does not change how the application runs.

#### 6.1.0

* Replaced `.laminas-ci/pre-run.sh` script with `.laminas-ci.json` config file https://github.com/dotkernel/admin/pull/378

#### 6.2.0

* Update badge for Packagist dependency version https://github.com/dotkernel/admin/pull/387

#### 7.0.0

* Updated readme, oss https://github.com/dotkernel/admin/pull/397

## FAQ

**Q: Where can I find the complete list of changes for the 7.0 upgrade?**

A: In the [Changelog](https://github.com/dotkernel/admin/blob/7.0/CHANGELOG.md).

**Q: Which PHP versions does 7.0 support, and do I have to move to PHP 8.4?**

A: The PHP constraint is `~8.2.0 || ~8.3.0 || ~8.4.0`, so 8.2 and 8.3 keep working and 8.4 is now supported.

**Q: Do I need a database migration?**

A: Yes, because of the `uuid` to `id` rename.
The `uuid_binary` column type becomes the native `uuid` type, and join columns are renamed, for example `userUuid` and `roleUuid` become `user_id` and `role_id`.
Write the migration by hand, because existing binary UUID data is not converted automatically and foreign keys must be recreated.
The `uuid` type declares a native `UUID` column, so check that your database supports it.

**Q: Which configuration files moved or changed?**

A: `config/autoload/app.global.php` and `config/migrations.php` are deleted.
In `config/autoload/local.php.dist` the database key `default` becomes `mariadb`, a `postgresql` entry is added and `charset` and `collate` become a single `collation` key.
`appName` moves into `local.php`, and without it the Twig titles render empty.
`config/autoload/templates.global.php` now sets the timezone to `UTC`.
The optional `table_prefix` key is added to the database configuration.

**Q: What must I rename after the route and handler renames?**

A: The authorization keys, the navigation `route_name` entries, the Twig templates, the handler classes and every test or custom code that references the old names.
Route URLs do not change.

**Q: Which login route rules should I end up with?**

A: The login form and login routes use `unauthenticated`, which is the final state after the 6.1.0 and 6.2.0 pull requests.

**Q: What happens to admins that are already logged in?**

A: `AdminIdentity` renames its constructor parameter and property from `uuid` to `id`, so existing sessions may break and admins may have to log in again.

**Q: Which behaviour changes should I look for in my own code?**

A: `UserStatusEnum::values()` now returns all cases, and the old result is available as `validValues()`.
Unknown settings now return 404 instead of 400.
Routes use `{id}` instead of `{uuid}`.
`UserResetPasswordService` and its interface are removed.

**Q: Are the optional updates required?**

A: No, they are CI, README and badge changes and do not change how the application runs.

**Q: How do I upgrade a project that is on 6.0?**

A: Apply the pull requests in release order: 6.1.0, then 6.2.0, then 7.0.0.
Read pull requests 371, 372 and 386 together, because the final login route rules are set by the last one.
Read pull requests 375 and 377 together, and pull requests 398 and 403 together.
