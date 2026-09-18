# Configuration Files

## Summary

This page explains which configuration keys to fill in for the application to send mail, including the `from` address and optional CC recipients.

## Mail

> If you intend to send emails from your Frontend, make sure to fill in SMTP connection params.
> This will be covered in the next section.

> **optional**: to run/create tests, duplicate `config/autoload/local.test.php.dist` as `config/autoload/local.test.php` this creates a new in-memory database that your tests will run on.

If you want your application to send mail, add valid credentials to the following keys in `config/autoload/mail.global.php`

Under `message_options` key:

- `from` - email address that will send emails (required)
- `from_name` - organization name for signing sent emails (optional)

> **Please add at least one email address in order for a contact message to reach someone**

Also feel free to add as many CCs as you require under the `dot_mail` => `default` => `message_options` => `cc` key.

## FAQ

**Q: Where do I configure mail credentials for the application?**

A: Add valid credentials to `config/autoload/mail.global.php`, filling in the `from` and `from_name` keys under `message_options`.

**Q: How do I set up an in-memory database for tests?**

A: Duplicate `config/autoload/local.test.php.dist` as `config/autoload/local.test.php`; this is optional and only needed to run or create tests.

**Q: Can I add multiple CC recipients for outgoing mail?**

A: Yes, add as many as you require under the `dot_mail` => `default` => `message_options` => `cc` key.
