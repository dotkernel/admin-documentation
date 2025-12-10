# Manage the GeoLite2 databases

You can download/update a specific GeoLite2 database, by running the following command where `{DATABASE}` can be `asn`, `city`, `country`:

```shell
php ./bin/cli.php geoip:synchronize -d {DATABASE}
```

You can download/update all GeoLite2 databases at once by running the following command:

```shell
php ./bin/cli.php geoip:synchronize
```

The output should be similar to the below, displaying per row: `database identifier`: `previous build datetime` -> `current build datetime`.

```shell
asn: n/a -> 2024-11-01 02:29:44
city: n/a -> 2024-11-01 02:29:31
country: n/a -> 2024-11-01 02:25:09
```

> `n/a` will be replaced by the older version of the GeoLite2 databases when you run the command again.

Get help for this command by running:

```shell
php ./bin/cli.php help geoip:synchronize
```

> If you set up the synchronizer command as a cronjob, you can add the `-q|--quiet` option, and it will output data only if an error has occurred.
