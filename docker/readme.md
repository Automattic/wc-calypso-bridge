# Running phpcs and phpunit with docker & docker-compose

## phpunit

To run the whole suite:

```
./docker/phpunit
```

Command arguments pass through:

```
./docker/phpunit --filter test_remove_paid_extension_upsells
```

## phpcs

To lint the whole plugin:

```
./docker/phpcs
```

Only what's changed from master:

```
./docker/phpcs-changed
```

## Mac OSX & Windows

On Docker Desktop for Mac & Windows you will need to add this plugin directory to your virtual machine directory shares.
