# Upgrades

Dotkernel Admin does not provide an automatic upgrade path.
Instead, the recommended procedure is to manually implement each modification listed in [releases](https://github.com/dotkernel/admin/releases).
Additionally, release info can also be accessed as an [RSS](https://github.com/dotkernel/admin/releases.atom) feed.

## Upgrade procedure

Once you clone Dotkernel Admin, you will find a [CHANGELOG.md](https://github.com/dotkernel/admin/blob/7.0/CHANGELOG.md) file in the root of the project.
This file contains a list of already implemented features in reverse chronological order.
You can use this file to track the version of Dotkernel Admin.

For each new release you need to implement the modifications from its pull requests in your project.
It is recommended to copy the release info into your project's CHANGELOG.md file.
This allows you to track your Admin's version and keep your project up to date with future releases.

## Version to version upgrading

Starting from [version 6.2](UPGRADE-7.0.md) the upgrading procedure is detailed version to version.
