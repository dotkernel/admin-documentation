# Composer Installation of Packages

## Summary

This page walks through installing Composer dependencies, answering the setup script's configuration prompts, and enabling or disabling development mode.

## Details

Composer is required to install Dotkernel Admin. You can install Composer from the [official site](https://getcomposer.org/).

> First, make sure that you have navigated your command prompt to the folder where you copied the files in the previous step.

## Install dependencies

Run this command in the command prompt.

> Use the **CLI** to ensure interactivity for proper configuration.

```shell
composer install
```

You should see this text below, along with a long list of packages to be installed instead of the `[...]`.
In this example there are 171 packages, though the number can change in future updates.
You will find the packages in the `vendor` folder.

```shell
No composer.lock file present. Updating dependencies to latest instead of installing from lock file. See https://getcomposer.org/install for more information.
Loading composer repositories with package information
Updating dependencies
Lock file operations: 171 installs, 0 updates, 0 removals
[...]
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 171 installs, 0 updates, 0 removals
[...]
```

The setup script prompts for some configuration settings, for example, the lines below:

```text
Please select which config file you wish to inject 'Laminas\Validator\ConfigProvider' into:
  [0] Do not inject
  [1] config/config.php
  Make your selection (default is 1):
```

Type `0` to select `[0] Do not inject`.

> We choose `0` because Dotkernel includes its own ConfigProvider, which already contains the prompted configurations.
> If you choose `[1] config/config.php`, an extra `ConfigProvider` will be injected.

The next question is:

```text
Remember this option for other packages of the same type? (y/N)
```

Type `y` here, and hit `enter` to complete this stage.

## Development mode

If you're installing the project for development, make sure you have development mode enabled by running:

```shell
composer development-enable
```

You can disable the development mode by running:

```shell
composer development-disable
```

You can check if you have development mode enabled by running:

```shell
composer development-status
```

## FAQ

**Q: How do I install the project's PHP dependencies?**

A: Run `composer install` from the command line, using the CLI to ensure interactivity for proper configuration.

**Q: Why should I select `[0] Do not inject` during setup?**

A: Dotkernel includes its own `ConfigProvider`, which already contains the prompted configurations, so injecting an extra one is unnecessary.

**Q: How do I check whether development mode is enabled?**

A: Run `composer development-status`; use `composer development-enable` or `composer development-disable` to toggle it.
