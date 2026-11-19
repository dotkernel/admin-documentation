# Doctrine ORM

## Summary

This page covers setting up the database connection, persistent connections, the second-level cache, creating and running migrations, and executing fixtures to populate the admin tables.

## Details

This step saves the database connection credentials in an Admin configuration file.
We do not cover the creation steps of the database itself.

## Setup database

Create a new **MariaDB**/**PostgreSQL** database and set its collation to `utf8mb4_general_ci`.

Make sure you fill out the database credentials in `config/autoload/local.php` under `$databases['mariadb']`.
Below is the item you need to focus on:

```php
$databases = [
    /**
     * You can add more database connections to this array.
     * Only one active connection is allowed at a time.
     * By default, the application uses the 'mariadb' connection.
     * You can switch to another connection by activating it under doctrine->connection->orm_default-->params.
     * Set 'persistent' to true to reuse database connections across requests (supported by pdo_mysql and pdo_pgsql).
     */
    'mariadb'    => [
        'host'         => 'localhost',
        'dbname'       => 'dotkernel',
        'user'         => '',
        'password'     => '',
        'port'         => 3306,
        'driver'       => 'pdo_mysql',
        'persistent'   => false,
        'collation'    => 'utf8mb4_general_ci',
        'table_prefix' => '',
    ],
    'postgresql' => [
        'host'         => '127.0.0.1',
        'dbname'       => 'dotkernel',
        'user'         => '',
        'password'     => '',
        'port'         => 5432,
        'driver'       => 'pdo_pgsql',
        'persistent'   => false,
        'collation'    => 'utf8mb4_general_ci',
        'table_prefix' => '',
    ],
];
```

> You can add more database connections to this array.
> Only one active connection is allowed at a time.
> By default, the application uses the 'mariadb' connection.
> You can switch to another connection by activating it under `doctrine` -> `connection` -> `orm_default` -> `params`.

### Persistent connections

Each entry in `$databases` has a `persistent` key, with the default value `false`.
Set it to `true` on the active connection to keep the database connection open after a request ends.
The next request that the same PHP process handles then uses this connection again and does not open a new one.

| Driver | `persistent` supported |
| --- | --- |
| `pdo_mysql`, `mysqli` | Yes |
| `pdo_pgsql` | Yes |
| `pgsql` | No |
| `pdo_sqlite`, `sqlite3` | No, the driver ignores the key |

> The `doctrine/dbal` documentation first describes persistent connections for MySQL and PostgreSQL in versions 4.3 and 3.10.
> Composer installs `doctrine/dbal` as a dependency of other packages.
> Run `composer show doctrine/dbal` to see your version.
> Older versions ignore the key.

Before you set the key to `true`, examine these items:

- **Connection limit:** each PHP-FPM worker keeps its own connection. The database `max_connections` value must be higher than the total number of workers in all pools and servers that connect to it.
- **State from the last request:** a connection that is used again keeps the state that the last request left on it. Examples are an open transaction after a fatal error, session variables, temporary tables and locks.
- **Idle timeouts:** the database can close a connection that stays idle for too long (the MariaDB `wait_timeout` value). The next request on that worker then fails with "server has gone away".
- **CLI:** commands get no benefit, because each command runs in its own process.
- **PostgreSQL:** a connection pooler such as PgBouncer is usually a better choice than persistent connections.

To make sure that the application uses connections again, send some requests and compare the connection IDs:

```sql
-- MariaDB
SHOW PROCESSLIST;

-- PostgreSQL
SELECT pid, backend_start FROM pg_stat_activity WHERE datname = 'dotkernel';
```

> If you installed the application before this setting was added, add `'persistent' => false,` to each entry in `$databases` in your `local.php` manually.

### Second-level cache

Starting with `doctrine/orm` 3.8.0, the Doctrine second-level cache needs the separate package `doctrine/orm-plugin-second-level-cache`.
The ORM only suggests that package, so Composer does not install it.
If the cache is enabled and the package is missing, Doctrine cannot create the entity manager, and the first request fails with this error:

```text
Service with name "doctrine.entity_manager.orm_default" could not be created.
Reason: Using the second level cache requires installing an additional package.
Install with composer require doctrine/orm-plugin-second-level-cache
```

The Core `ConfigProvider` enables the cache by default.
`config/autoload/local.php` disables it with the `enabled` key set to `false`.
The application loads `local.php` after the Core configuration.
This file changes only the `enabled` key, so the other second-level cache settings still come from Core:

```php
'doctrine' => [
    'connection'    => [
        // ...
    ],
    'configuration' => [
        'orm_default' => [
            'second_level_cache' => [
                'enabled' => false,
            ],
        ],
    ],
],
```

The default application does not use the second-level cache.
No entity has `#[ORM\Cache]`, and no query calls `setCacheable(true)`.
The repositories use the query cache (`useQueryCache(true)`), which is a different cache. The `enabled` key has no effect on it.

To enable the second-level cache, install the plugin and then set `enabled` to `true`:

```shell
composer require doctrine/orm-plugin-second-level-cache:^1.0
```

> The installation script copies `config/autoload/local.php` from `local.php.dist` only one time, during the installation.
> If you installed the application before this setting was added, add the `configuration` block to your `local.php` manually.
> If configuration caching is enabled, delete `data/cache/config-cache.php` after this change.

### Creating migrations

Create a database migration by executing the following command:

```shell
php ./vendor/bin/doctrine-migrations diff
```

The new migration file will be placed in `src/Core/src/App/src/Migration/`.

### Running migrations

Run the database migrations by executing the following command:

```shell
php ./vendor/bin/doctrine-migrations migrate
```

> If you have already run the migrations, you may get the below message:

```text
WARNING! You have x previously executed migrations in the database that are not registered migrations.
  {migration list}
Are you sure you wish to continue? (y/n)
```

> In this case, you should double-check to make sure the new migrations are ok to run.

When using an empty database, you will get this confirmation message:

```text
WARNING! You are about to execute a migration in database "<your_database_name>" that could result in schema changes and data loss. Are you sure you wish to continue? (yes/no)
```

Hit `Enter` to confirm the operation.
This will run all the migrations in chronological order.
Each migration will be logged in the `migrations` table to prevent running the same migration more than once, which is often not desirable.

If everything ran correctly, you will get this confirmation.

```text
[OK] Successfully migrated to version: Core\App\Migration\VersionYYYYMMDDHHMMSS
```

### Fixtures

Run this command to populate the admin tables with the default values:

```shell
php ./bin/doctrine fixtures:execute
```

You should see our galloping horse in the command line.

```shell
Executing Core\App\Fixture\AdminRoleLoader
Executing Core\App\Fixture\OAuthClientLoader
Executing Core\App\Fixture\OAuthScopeLoader
Executing Core\App\Fixture\UserRoleLoader
Executing Core\App\Fixture\AdminLoader
Executing Core\App\Fixture\UserLoader
Fixtures have been loaded.
                .''
      ._.-.___.' (`\
     //(        ( `'
    '/ )\ ).__. )
    ' <' `\ ._/'\
       `   \     \
```

## FAQ

**Q: Which database engines are supported?**

A: You can create a **MariaDB** or **PostgreSQL** database, and its collation should be set to `utf8mb4_general_ci`.

**Q: Where do I put my database connection credentials?**

A: Fill them out in `config/autoload/local.php`, under `$databases['mariadb']` (or `$databases['postgresql']` if using PostgreSQL).

**Q: Should I enable persistent connections?**

A: Enable them only after you make sure that the database `max_connections` value is sufficient for all the PHP-FPM workers that connect to it.
Persistent connections remove the cost of a new connection on each request, but each worker then keeps a connection open all the time.
See [Persistent connections](#persistent-connections).

**Q: I set `persistent` to `true`, but each request still opens a new connection. Why?**

A: Make sure that you changed the entry that `doctrine.connection.orm_default.params` points to.
Make sure that the driver is `pdo_mysql`, `mysqli` or `pdo_pgsql`.
Make sure that `doctrine/dbal` is version 4.3 (or 3.10) or later.
If configuration caching is enabled, delete `data/cache/config-cache.php`.

**Q: The first request fails with "Using the second level cache requires installing an additional package". What is wrong?**

A: Composer installed `doctrine/orm` 3.8.0 or later, and your configuration still enables the second-level cache.
These ORM versions need `doctrine/orm-plugin-second-level-cache` for that cache.
Set `doctrine.configuration.orm_default.second_level_cache.enabled` to `false` in `config/autoload/local.php`.
If you need the cache, install the plugin instead.
See [Second-level cache](#second-level-cache).

**Q: Does the application become slower when the second-level cache is disabled?**

A: Not in the default application, because it does not keep entities in that cache.
The cache is important only if you add `#[ORM\Cache]` to entities or call `setCacheable(true)` on queries.

**Q: How do I create and run a migration?**

A: Run `php ./vendor/bin/doctrine-migrations diff` to generate the migration file, then `php ./vendor/bin/doctrine-migrations migrate` to apply it.

**Q: How do I populate the admin tables with default data?**

A: Run `php ./bin/doctrine fixtures:execute` to load the fixtures.
