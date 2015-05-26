# Deployer Extensions

[![Total downloads](https://img.shields.io/packagist/dt/minetro/deployer-extension.svg?style=flat)](https://packagist.org/packages/minetro/deployer-extension)
[![Latest stable](https://img.shields.io/packagist/v/minetro/deployer-extension.svg?style=flat)](https://packagist.org/packages/minetro/deployer-extension)

[Ftp-Deployment](https://github.com/dg/ftp-deployment) Extension for Nette. 

## Install
```sh
$ composer require minetro/deployer-extension:~1.0.0
```

## Configuration

### Register extension
```yaml
extensions:
    deployer: Minetro\Deployer\DI\DeployerExtension
```

### Configure extension

Detailed configuration is described here [ftp-deployment](https://github.com/dg/ftp-deployment).

```yaml
parameters:
    deploy:
        protocol: ftp # ftp|ftps
        user: user1
        password: mysecretpwd
        scheme: example.com # example.com/www     

deployer:
    config:
        mode: run
        logFile: %appDir%/log/deployer.log
        tempDir: %appDir%/temp
        colors: off

    sections:
        web1:
            remote: %deploy.protocol%://%deploy.user%:%deploy.password%@%deploy.scheme%
            local: %wwwDir%
            testMode: false

            allowdelete: on
            passiveMode: on
            preprocess: off

            ignore:
                # Common
                - .git*
                - .idea*
                - .bowerrc
                - composer.*
                - bower.json
                - gulpfile.js
                - package.json

                # Application
                - /app/config/config.local.neon
                - /bin
                - /tests
                - /node_modules
                - /log/*
                - "!/log/.htaccess"
                - /temp/*
                - "!/temp/.htaccess"

                # Public
                - /www/*.scss
                - /www/*.less
                - /www/temp
                - /www/uploaded
                - /www/stats

            before:
                #- [@\TestBeforeListener, onBefore]
            after:
                #- [@\TestAfterListener, onAfter]

            purge:
                - temp/cache
                - temp/myfolder
```

### More webs <=> more sections

```yaml
deployer:
    section:
        example.com:
            ...
        test.cz:
            ...
```

## Listeners

You can register service which implement `AfterListener` or `BeforeListener`.

Example you can [find here](https://github.com/minetro/deployer-extension/tree/master/examples).

### `ComposerInstallListener`

This is prepared listener that runs command:

```sh
composer install --no-dev --prefer-dist --optimize-autoloader -d $DIR
```

#### Parameters

- `$DIR` is **section.local**

### `ComposerUpdateListener`

This is prepared listener that runs command:

```sh
composer update --no-dev --prefer-dist --optimize-autoloader -d $DIR
```

#### Parameters

- `$DIR` is **section.local**

## Deploy

See example [scripts here](https://github.com/minetro/deployer-extension/tree/master/examples). 

### Automatic

Config is automatic passed via extension.

```php
# Create Deploy Manager
$dm = $container->getByType('Minetro\Deployer\Manager');
$dm->deploy();
```

### Manual

You have to create your configuration by yourself.

```php
# Create config
$config = new Config();
$config->setLogFile(..);
$config->setMode(..);

$section = new Section();
$section->setName(..);
$config->addSection($section);
```

```php
# Create Deploy Manager
$dm = $container->getByType('Minetro\Deployer\Manager');
$dm->manualDeploy($config);
```

```php
# Inject Deploy Manager
use Minetro\Deployer;

/** @var Deployer\Manager @inject */
public $dm;

public function actionDeploy() 
{
    $this->dm->manulDeploy($config);
}
```

### Prepared deploy script ([deploy.php](https://github.com/minetro/deployer-extension/tree/master/examples/deploy.php) & [deploy](https://github.com/minetro/deployer-extension/tree/master/examples/deploy))

Place it by yourself (for example root/deploy.php). Be careful about `local` and `tempDir`, there depend on location.

```php
require __DIR__ . '/vendor/autoload.php';

# Configurator
$configurator = new Nette\Configurator;
$configurator->setDebugMode(TRUE);
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

# Configs
$configurator->addConfig(__DIR__ . '/config/config.neon');

# Create DI Container
$container = $configurator->createContainer();

# Create Deploy Manager
$dm = $container->getByType('Minetro\Deployer\Manager');
$dm->deploy();
```