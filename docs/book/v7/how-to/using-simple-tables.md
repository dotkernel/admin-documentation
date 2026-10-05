# Use simple tables

## Summary

This page explains how to build a list page as a simple table, with sortable columns, row selection and column visibility that is remembered for each admin.

## Details

Simple tables are used by the list pages of Dotkernel Admin, such as the lists of admins, admin logins and users.
Each table is made of these parts:

- a Twig template that renders the table, using the `sortableColumn` macro from `@partial/macros.html.twig`
- the `table_settings.js` script, which shows or hides columns and enables the edit and delete buttons
- the `Setting` entity, which stores the selected columns for each admin and table
- two routes that read and write the setting: `setting::view-setting` (`GET /{identifier}`) and `setting::store-setting` (`POST /{identifier}`)

When the page loads, `table_settings.js` requests the saved column selection, applies it, then displays the table.
Whenever the admin toggles a column, the script saves the new selection.
If no selection was saved yet, all columns are displayed.

The examples below build a list page for books.
For a complete module, see [Creating a book module using DotMaker](../tutorials/create-book-module-via-dot-maker.md).

## Create a simple table

Creating a simple table requires four steps:

- add a setting identifier
- send the identifier to the template
- render the table
- load the scripts

### Add a setting identifier

Each table stores its selected columns under its own identifier.
Open `src/Core/src/Setting/src/Enum/SettingIdentifierEnum.php` and add a new case:

```php
case IdentifierTableBookListSelectedColumns = 'table_book_list_selected_columns';
```

The `identifier` column of the `settings` table is a database `ENUM`, so the new case requires a migration.
Generate it, check that it alters the `settings.identifier` column, then run it:

```shell
php ./vendor/bin/doctrine-migrations diff
php ./vendor/bin/doctrine-migrations migrate
```

See [Create Database Migrations](creating-migrations.md) for more details.

### Send the identifier to the template

In the handler that renders the list, send the identifier to the template:

```php
return new HtmlResponse(
    $this->template->render('book::list-book', [
        'pagination' => $this->bookService->getBooks($request->getQueryParams()),
        'identifier' => SettingIdentifierEnum::IdentifierTableBookListSelectedColumns->value,
    ])
);
```

### Render the table

In the template, import the macro and render the column selector and the table:

```html
{% from '@partial/macros.html.twig' import sortableColumn %}

<div class="dropdown">
    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-columns"></i>
    </button>
    <ul class="dropdown-menu" id="column-selector"></ul>
</div>

<table id="book-table" class="table table-bordered table-hover table-striped table-light" style="display: none;">
    <thead>
    <tr>
        <th class="column-book-uuid"></th>
        <th class="column-book-name">
            {{ sortableColumn('book::list-book', {}, pagination.queryParams, 'book.name', 'Name') }}
        </th>
        <th class="column-book-release-date">
            {{ sortableColumn('book::list-book', {}, pagination.queryParams, 'book.release-date', 'Release Date') }}
        </th>
    </tr>
    </thead>
    <tbody>
    {% for book in pagination.items %}
    <tr class="table-row">
        <td class="column-book-uuid" style="width: 1vw;">
            <label>
                <input type="checkbox"
                       class="checkbox ui-checkbox"
                       value="{{ book.uuid }}"
                       data-edit-url="{{ path('book::edit-book', {uuid: book.uuid}) }}"
                       data-delete-url="{{ path('book::delete-book', {uuid: book.uuid}) }}">
            </label>
        </td>
        <td class="column-book-name">{{ book.name }}</td>
        <td class="column-book-release-date">{{ book.releaseDate|date('Y-m-d') }}</td>
    </tr>
    {% endfor %}
    </tbody>
</table>
```

Things to note:

- the table is hidden with `style="display: none;"`, the script displays it after applying the saved selection
- the `#column-selector` list is empty, the script fills it with one checkbox for each sortable column
- the first column holds the row checkbox and has no `sortableColumn`, so it cannot be hidden

### Load the scripts

At the end of the template, define the three variables that `table_settings.js` requires, then load the script:

```html
{% block javascript %}
{{ parent() }}
<script>
    const tableId = '#book-table';
    const storeSettingsUrl = '{{ path('setting::store-setting', {identifier: identifier}) }}';
    const getSettingsUrl = '{{ path('setting::view-setting', {identifier: identifier}) }}';
</script>
<script type="module" src="{{ asset('js/table_settings.js') }}"></script>
{% endblock %}
```

If any of the three variables is missing, the script logs an error in the browser console and does nothing.
Any page specific script, such as `book.js`, is loaded the same way, using `type="module"`.
It must be registered in the `entries` object of `vite.config.js` and built, see [Use NPM Commands](npm_commands.md).

## Name the columns

The `sortableColumn` macro receives the sort key as its fourth argument.
It adds the `table-column` class to the header link and sets its `data-column` attribute to the sort key, with dots replaced by dashes.
The script hides and shows a column by the class `column-<data-column>`, so every `<th>` and `<td>` of that column must use the same class:

| Sort key | `data-column` | Class |
| --- | --- | --- |
| `user.identity` | `user-identity` | `column-user-identity` |
| `detail.firstName` | `detail-firstName` | `column-detail-firstName` |
| `book.release-date` | `book-release-date` | `column-book-release-date` |

## Edit and delete buttons

`table_settings.js` also handles row selection:

- clicking a row (`.table-row`) toggles its checkbox
- the buttons with the ids `btn-edit-resource` and `btn-delete-resource` are enabled only while exactly one `.ui-checkbox` is checked

The URLs of the selected row are read from the `data-edit-url` and `data-delete-url` attributes of the checked box.
Opening the modals that use them is done by the page specific script, see the `_book.js` file in [Creating a book module using DotMaker](../tutorials/create-book-module-via-dot-maker.md).

## FAQ

**Q: Why is a column always displayed, even if I untick it in the column selector?**

A: The `<th>` or `<td>` class does not match the column's `data-column` value.
Check that every cell of that column uses `column-` followed by the sort key with dots replaced by dashes.

**Q: Why is the column selector empty?**

A: The selector is built from the elements with the `table-column` class.
Make sure the headers use the `sortableColumn` macro and that the template contains an empty `<ul id="column-selector">`.

**Q: Why is the table not displayed at all?**

A: The table stays hidden until `table_settings.js` runs.
Check the browser console for errors about `tableId`, `storeSettingsUrl` or `getSettingsUrl`, and make sure the script is loaded with `type="module"`.

**Q: Why are my column selections not saved?**

A: The identifier is probably not a case of `SettingIdentifierEnum`, or the migration that adds it to the `settings.identifier` column was not run.
In both cases, the setting routes respond with an error, which you can see in the browser's network tab.

**Q: Why are all columns displayed again after a reload?**

A: When the saved selection is empty, all columns are displayed.
This is also the state of an admin that never changed the selection for that table.
