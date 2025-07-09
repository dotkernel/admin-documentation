# CSRF protection in forms

A Cross-Site Request Forgery (CSRF) attack is a type of security vulnerability that tricks a user into performing
actions on a web application in which they are authenticated, without their knowledge or consent.

Web applications can protect users against these types of attacks by implementing CSRF tokens in their forms which are
known only to the application that generated them and must be included when submitting forms. With each visit, a new
CSRF token is added to the form so tokens are not reusable between forms. Missing to provide a valid CSRF token will
result in a form validation error.

## Implement CSRF protection

Implementing CSRF protection requires three steps:

- create new field using [laminas/laminas-form](https://github.com/laminas/laminas-form)'s [CSRF](https://github.com/laminas/laminas-form/blob/3.21.x/src/Element/Csrf.php) element
- validate new field using [laminas/laminas-session](https://github.com/laminas/laminas-session)'s
[CSRF](https://github.com/laminas/laminas-session/blob/2.22.x/src/Validator/Csrf.php) validator
- render field using [laminas/laminas-form](https://github.com/laminas/laminas-form)'s [FormElement](https://github.com/laminas/laminas-form/blob/3.21.x/src/View/Helper/FormElement.php) helper

### Create field

Open the form's PHP class and append the following code to the method that initializes the fields (usually `init`):

```php
$this->add(
    (new Csrf('exampleCsrf'))
        ->setOptions([
            'csrf_options' => ['timeout' => 3600, 'session' => new Container()],
        ])
        ->setAttribute('required', true)
);
```

where `exampleCsrf` should be a suggestive name that describes the purpose of the field (example: `forgotPasswordCsrf`).

### Validate field

Open the InputFilter that validates the form fields and append the following code to the method that initializes the
fields (usually `init`):

```php
$this->add(new CsrfInput('exampleCsrf'));
```

where `exampleCsrf` must match the CSRF field's name in the form.

> Don't forget to modify both occurrences in this file.

> Make sure that you validate the form using its `isValid` method in the handler/controller where it is submitted.

### Render field

Open the template that renders your form and add the following code somewhere between the form's opening and closing
tags:

```text
{{ formElement(form.get('exampleCsrf')) }}
```

## Test the implementation

Access your form from the browser and view its source. You should see a new hidden field, called `exampleCsrf` (or
however you named it). After filling out the form, submitting it should work as before.

In order to make sure that the new CSRF field works as expected, you can inspect the form using your browser's
`Developer tools` and modify its value in any way. Submitting a filled out form should result in a validation error:

> This field is required and cannot be empty.

### Timeout

Note the `timeout` option in your PHP form's `exampleCsrf` field, with its default value set to **3600**. This
represents the value in seconds for how long the token is valid. Submitting a form that has been rendered for longer
than this value will result in a validation error:

> Invalid CSRF.

You can modify the value of `timeout` in each form, but the default value should work in most cases.
