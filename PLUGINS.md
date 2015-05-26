# Deployer Extension :: PLUGINS

## `MaintenanceListener`

This is prepared listener that help make maintenance mode easier.

Plugin has two sections **rewrite** and **rename**. 

You have to register to `before` and to `after` also (!).

### Rewrite

Before: *backup origin file, rename destination file to source file*
After: *revert rewriting*

```yaml
deployer:
    plugins:
    
        maintenance:
            rewrite:
                - [www/index.php, www/index.maintenance]
```

### Rename

Before: *rename origin file to destination file*
After: *revert renaming*

```yaml
deployer:
    plugins:
    
        maintenance:
            rename:
                - [www/.maintenance.php, www/maintenance.php]
```

You can combine rewriting and renaming together. 

## `ComposerInstallListener`

This is prepared listener that runs command:

```sh
composer install --no-dev --prefer-dist --optimize-autoloader -d $DIR
```

### Parameters

- `$DIR` is **section.local**

## `ComposerUpdateListener`

This is prepared listener that runs command:

```sh
composer update --no-dev --prefer-dist --optimize-autoloader -d $DIR
```

### Parameters

- `$DIR` is **section.local**