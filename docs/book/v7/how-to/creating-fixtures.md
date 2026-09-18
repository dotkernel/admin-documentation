# Fixtures

## Summary

This page explains how to seed the database with initial values using `dotkernel/dot-data-fixtures`, and how to list or execute fixtures with the Doctrine CLI command.

## Details

> Fixtures are used to seed the database with initial values and should only be executed ONCE each, after migrating the database.

Seeding the database is done with the help of our custom package `dotkernel/dot-data-fixtures` built on top of `doctrine/data-fixtures`.
See below on how to use our CLI command for listing and executing Doctrine data fixtures.

## Working with fixtures

You can find an example of a fixtures class in `src/Core/src/App/src/Fixture/AdminLoader.php`.

To list all the available fixtures by order of execution, run:

```shell
php ./bin/doctrine fixtures:list
```

To execute all fixtures, run:

```shell
php ./bin/doctrine fixtures:execute
```

To execute a specific fixture, use its class name, like in this example:

```shell
php ./bin/doctrine fixtures:execute --class=AdminLoader
```

Fixtures can and should be ordered to ensure database consistency.
More on ordering fixtures can be found here:
https://www.doctrine-project.org/projects/doctrine-data-fixtures/en/latest/how-to/fixture-ordering.html#fixture-ordering

## FAQ

**Q: How many times should a fixture be executed?**

A: Each fixture should only be executed ONCE, and only after the database has been migrated.

**Q: How do I see which fixtures are available?**

A: Run `php ./bin/doctrine fixtures:list` to list all available fixtures in their order of execution.

**Q: Can I execute a single fixture instead of all of them?**

A: Yes, run `php ./bin/doctrine fixtures:execute --class=AdminLoader`, replacing `AdminLoader` with the class name of the fixture you want to run.
