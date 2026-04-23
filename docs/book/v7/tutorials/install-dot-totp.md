# Installing dot-totp into Dotkernel Admin

If you haven't already, install [Dotkernel Admin](https://github.com/dotkernel/admin).

> The installation steps listed below should work similarly in any middleware-based application.

The first step is to include the package in your project by running this command:

```shell
composer require dotkernel/dot-totp
```

We will follow the Dotkernel file structure and create the files in the list below.
If you follow the links from the [main totp integration example](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp), you can download the files and add them to your codebase.

- [src/Admin/src/Form/RecoveryForm.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Form/RecoveryForm.php)
- [src/Admin/src/Form/TotpForm.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Form/TotpForm.php)
- [src/Admin/src/Handler/Account/GetDisableTotpFormHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/GetDisableTotpFormHandler.php)
- [src/Admin/src/Handler/Account/GetEnableTotpFormHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/GetEnableTotpFormHandler.php)
- [src/Admin/src/Handler/Account/GetRecoveryFormHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/GetRecoveryFormHandler.php)
- [src/Admin/src/Handler/Account/GetTotpHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/GetTotpHandler.php)
- [src/Admin/src/Handler/Account/PostDisableTotpHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/PostDisableTotpHandler.php)
- [src/Admin/src/Handler/Account/PostEnableTotpHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/PostEnableTotpHandler.php)
- [src/Admin/src/Handler/Account/PostValidateRecoveryHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/PostValidateRecoveryHandler.php)
- [src/Admin/src/Handler/Account/PostValidateTotpHandler.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/Handler/Account/PostValidateTotpHandler.php)
- [src/Admin/src/InputFilter/RecoveryInputFilter.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/InputFilter/RecoveryInputFilter.php)
- [src/Admin/src/InputFilter/TotpInputFilter.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/src/InputFilter/TotpInputFilter.php)
- [src/Admin/templates/admin/recovery-form.html.twig](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Admin/templates/admin/recovery-form.html.twig)
- [src/App/src/Middleware/CancelUrlMiddleware.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/App/src/Middleware/CancelUrlMiddleware.php)
- [src/App/src/Middleware/TotpMiddleware.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/App/src/Middleware/TotpMiddleware.php)

You can use the trait at [src/Core/src/App/src/Entity/TotpTrait.php](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/src/Core/src/App/src/Entity/TotpTrait.php) in any entity where you need 2FA.

> Make sure to migrate the new columns `totpSecret`, `totp_enabled` and `recovery_codes` in your entity.

There are still some code snippets in the [_misc](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp) folder:

- [the enable/disable 2FA button](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/_misc/totp-append-view-account.html.twig) should be used in the `view-account.html.twig` file or in a new page.
- [the routes updates](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/_misc/totp-append-routes.php) must be added in the `src/Admin/src/RoutesDelegator.php` file.
- [the pipeline updates](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/_misc/totp-append-Pipeline.php) must be added in the `config/pipeline.php` file after `$app->pipe(AuthMiddleware::class);`.
- [the ConfigProvider updates](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/_misc/totp-append-ConfigProvider.php) must be added in the `src/Admin/src/ConfigProvider.php` file.
- [append these routes](https://github.com/dotkernel/admin-documentation/tree/main/code_examples/totp/_misc/totp-append-authorization-guards.global.php) to your `authorization-guards.global.php` file.
- Add the constant below in `src/Core/src/App/src/Message.php` to return an error message when the recovery code is invalid.

```php
public const VALIDATOR_INVALID_CODE = 'Invalid recovery code.'
``` 

## Dot-totp in Action

Once you have `dot-totp` implemented, you can activate the feature in your admin accounts.
If you navigate to your profile from the top-right image in Dotkernel Admin, you should see this box.

![profile-totp-deactivated!](https://docs.dotkernel.org/img/admin/v7/install-totp/profile-totp-deactivated.jpg)

Simply click on 'Enable TOTP' to begin the activation process.

> We blurred out the QR code and recovery codes for this tutorial.
> You will receive dynamically generated versions that will be fully visible to you.

> You will need to have an Authenticator app installed on your mobile device.

![totp-activate-qr!](https://docs.dotkernel.org/img/admin/v7/install-totp/totp-activate-qr.jpg)

Follow the instructions on the screen:

- Scan the QR code with your mobile device.
- Enter the 6-digit code it generates on your mobile device.

> The code refreshes every 30 seconds.

The TOPT activation flow will list several recovery codes you can use if your mobile device isn't available.

![totp-recovery-codes!](https://docs.dotkernel.org/img/admin/v7/install-totp/totp-recovery-codes.jpg)

> Each recovery code is usable only once.

> Save the recovery codes in a secure location.

If the code is valid, you will be logged in, and TOTP will be activated for your account.

Whenever you need to log into the account, you will start by entering your username and password, like before.
Since TOTP is activated, you will need to also submit the code from your Authenticator app.
Alternatively, you can submit a recovery code.

![totp-ask-code!](https://docs.dotkernel.org/img/admin/v7/install-totp/totp-ask-code.jpg)

That's it!
You are now logged in securely.
