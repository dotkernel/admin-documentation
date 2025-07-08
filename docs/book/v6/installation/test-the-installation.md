# Running the application

> **Do not enable dev mode in production**

We recommend running your applications in WSL:

- Make sure you have [WSL](https://github.com/dotkernel/development/blob/main/wsl/README.md) installed on your system.
- Currently we provide a distro implementations for [AlmaLinux9](https://github.com/dotkernel/development/blob/main/wsl/os/almalinux9/README.md).
- Install the application in a virtualhost as recommended by the chosen distro.
- Set `$baseUrl` in **config/autoload/local.php** to the address of the virtualhost.
- Run the application by opening the virtualhost address in your browser.

You should see the `Dotkernel admin` login page.

> If you are getting exceptions or errors regarding some missing services, try running the following command:

```shell
sudo php bin/clear-config-cache.php
```

> If `config-cache.php` is present that config will be loaded regardless of the `ConfigAggregator::ENABLE_CACHE` in `config/autoload/mezzio.global.php`

If you ran the fixtures you will have an admin user in the database with the following credentials:

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

Do not change this in `local.php.dist` as well because this value should remain `true` on production.
