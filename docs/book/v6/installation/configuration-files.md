# Configuration Files

## Mail

> If you intend to send emails from your Frontend, make sure to fill in SMTP connection params.
> This will be covered in the next section.

> **optional**: in order to run/create tests, duplicate `config/autoload/local.test.php.dist` as `config/autoload/local.test.php`
> this creates a new in-memory database that your tests will run on.

If you want your application to send mail, add valid credentials to the following keys in `config/autoload/mail.global.php`

Under `message_options` key:

- `from` - email address that will send emails (required)
- `from_name` - organization name for signing sent emails (optional)

Under `smtp_options` key:

- `host` - hostname or IP address of the mail server (required)
- `connection_config` - add the `username` and `password` keys (required)

In `config/autoload/local.php` edit the key `contact` => `message_receivers` => `to` with *string* values for emails that should receive contact messages.

> **Please add at least 1 email address in order for contact message to reach someone**

Also feel free to add as many CCs as you require under the `contact` => `message_receivers` => `cc` key.
