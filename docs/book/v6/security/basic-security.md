# Basic Security Observations

`dotkernel/admin` provides all necessary tools to implement safe applications, however you will need to manually make use of some of them.
This section will go over the provided tools and any steps you need to follow in order to use them successfully, as well as a few general considerations.

## Form Input Validation

In order to create safe forms, `dotkernel/admin` makes use of [laminas/laminas-form](https://github.com/laminas/laminas-form) and [laminas/laminas-inputfilter](https://github.com/laminas/laminas-inputfilter).
All shipped forms have their inputs validated, and it is strongly recommended any custom forms added also make use of input filters to validate user input.

## Cross-Site Request Forgery Protection

`dotkernel/admin` provides protection against CSRF attacks by using CSRF token creation and validation, available for all forms.

All forms provided make use of CSRF token validation, but you must ensure to implement this step for any new forms you create.

> This step is described in the [Set Up CSRF](../how-to/csrf.md) tutorial.

## Role-Based Access Control

This project makes use of [dot-rbac-guard](https://github.com/dotkernel/dot-rbac-guard) and [dot-rbac](https://github.com/dotkernel/dot-rbac) to handle access control.

The default modules have already been configured, but any custom functionality will require additional configuration to make sure it is protected.
Update the configuration files of both these packages whenever you add new routes or roles.

> This step is described in the [Configure Authorizations](../how-to/authorization.md) tutorial.

## Session and Cookie Settings

Make sure your session cookie settings are properly set up for usage in production by reviewing the `config/autoload/session.global.php` file.
Pay extra attention to the following keys, to make sure your desired values are set:

- `session_config.cookie_httponly`
- `session_config.cookie_samesite`
- `session_config.cookie_secure`

## Demo Credentials

`Admin` ships with a demo admin account, with public identity and password.
**Make sure to change or remove this demo account when going live.**

## PHP Dependencies

`dotkernel/admin` uses `composer` to handle PHP dependencies.
In time, make sure to review any common vulnerabilities and exposures for your dependencies.

> You may also keep an eye on the `dotkernel/admin` changelog for any updates relevant to your project.

## JavaScript Dependencies

This project uses `npm` to handle JavaScript dependencies.
Keep an eye on any vulnerabilities whenever using `npm` to install or update packages.

> You may use the `npm audit` command to check for vulnerabilities in the current `node_modules`.

## General Considerations

- `*.global.php` and `*.php.dist` configuration files are visible to the VCS, make sure **not** to include sensitive data in commits.
    - `*.local.php` configuration files are ignored by the VCS by default and are the recommended place for sensitive data such as API keys.
- Review `config/autoload/cors.global.php` to ensure your application is accessible by your preferred origins.
- Make sure the `development mode` is correctly set - **do not** enable `development mode` in a production environment.
    - You can use the following command to check the current status:

```shell
composer development-status
```

- `Admin` ships with a [Laminas Continuous Integration GitHub Action](https://github.com/laminas/laminas-continuous-integration-action), consider keeping it in your custom applications to ensure code quality.

> Read more about the benefits of using [Laminas Continuous Integration](https://getlaminas.org/blog/2024-09-26-using-laminas-continuous-delivery-and-deployment.html).
