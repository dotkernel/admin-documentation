# Creating migrations

## Summary

This page explains how to generate a new database migration file and how to add schema changes to its `up` and `down` methods.

## Details

Migrations are used to create and/or edit the database structure.
To generate a new migration file, use this command:

```shell
php ./vendor/bin/doctrine-migrations migrations:generate
```

It creates a PHP file like this one `src/Core/src/App/src/Migration/Version20240627134952.php` that can then be edited in the IDE.
You can add new queries in:

- `public function up` - these are executed when the migration is run.
- `public function down` - these are optional queries that undo the above changes.

## Example

This example creates a new column named `test`.
Add this in `public function up`:

```shell
$this->addSql('ALTER TABLE admin ADD test VARCHAR(255) NOT NULL');
```

And its opposite in `public function down`:

```shell
$this->addSql('ALTER TABLE admin DROP test');
```

## FAQ

**Q: How do I generate a new migration file?**

A: Run `php ./vendor/bin/doctrine-migrations migrations:generate`, which creates a new PHP file under `src/Core/src/App/src/Migration/`.

**Q: What is the difference between `up` and `down`?**

A: The `up` method contains the queries that are executed when the migration runs, and the `down` method contains the optional queries that undo those changes.
