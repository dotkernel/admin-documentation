# Testing (Running)

Note: **Do not enable dev mode in production**

- Run the following command in your project's directory to start PHPs built-in server:

      php -S 0.0.0.0:8080 -t public

> Running command `composer serve` will do the exact same, but the above is faster.

`0.0.0.0` means that the server is open to all incoming connections
`127.0.0.1` means that the server can only be accessed locally (localhost only)
`8080` the port on which the server is started (the listening port for the server)

**NOTE:**
If you are still getting exceptions or errors regarding some missing services, try running the following command

    php bin/clear-config-cache.php

> If `config-cache.php` is present that config will be loaded regardless of the `ConfigAggregator::ENABLE_CACHE`
> in `config/autoload/mezzio.global.php`

- Open a web browser and visit `http://localhost:8080/`

You should see the `Dotkernel admin` login page.

If you ran the migrations you will have an admin user in the database with the following credentials:

- **User**: `admin`
- **Password**: `dotadmin`

**NOTE:**

- **Production only**: Make sure you modify the default admin credentials.
- **Development only**: `session.cookie_secure` does not work locally so make sure you modify your `local.php`, as per the following:

      return [
        'session_config' => [
            'cookie_secure' => false,
        ]
      ];

Do not change this in `local.php.dist` as well because this value should remain `true` on production.
