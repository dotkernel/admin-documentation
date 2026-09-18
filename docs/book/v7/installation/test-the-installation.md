# Running the application

## Summary

This page explains how to run the application in WSL, troubleshoot common startup issues, and locate the default admin credentials created by the fixtures.

## Details

> **Do not enable dev mode in production**

We recommend running your applications in WSL:

- Make sure you have [WSL2](https://docs.dotkernel.org/development/v2/setup/system-requirements/) installed on your system.
- Currently, we provide a distro implementation for [AlmaLinux 10](https://docs.dotkernel.org/development/v2/running/).
- Install the application in a virtualhost as recommended by the chosen distro.
- Set `$baseUrl` in **config/autoload/local.php** to the address of the virtualhost.
- Run the application by opening the virtualhost address in your browser.

> If you are getting server error 500, please check the folders permissions [Common permission issues](https://docs.dotkernel.org/development/v2/faq/#how-do-i-fix-common-permission-issues)

You should see the `Dotkernel Admin` login page.

> If you are getting exceptions or errors regarding some missing services, try running the following command:

```shell
sudo php ./bin/clear-config-cache.php
```

> If `config-cache.php` is present that config will be loaded regardless of the `ConfigAggregator::ENABLE_CACHE` in `config/autoload/mezzio.global.php`

If you ran the fixtures, you will have an admin user in the database with the following credentials:

- **User**: `admin`
- **Password**: `dotadmin`

> **Production only**: Make sure you modify the default admin credentials.

> **Development only**: `session.cookie_secure` does not work locally so make sure you modify your `local.php`, as per the following:

```php
# other code

return [
    # other configurations...
    'session_config' => [
        'cookie_secure' => false,
    ],
];
```

> Do not change this in `local.php.dist` as well because this value should remain `true` on production.

## FAQ

**Q: What are the default admin credentials after running the fixtures?**

A: **User**: `admin`, **Password**: `dotadmin`.
Make sure to change these before going to production.

**Q: What should I do if I get a server error 500?**

A: Check the folder permissions; see the linked common permission issues FAQ for guidance.

**Q: What should I do if I get exceptions about missing services?**

A: Run `sudo php ./bin/clear-config-cache.php` to clear the config cache.

**Q: Why doesn't `session.cookie_secure` work locally?**

A: It does not work in local development, so you must set it to `false` in `local.php` (never in `local.php.dist`, since it must remain `true` in production).
