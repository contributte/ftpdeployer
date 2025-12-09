![](https://heatbadger.vercel.app/github/readme/contributte/ftpdeployer/?deprecated=1)

<p align=center>
    <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
    <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
    <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
    Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

## Disclaimer

| :warning: | This project is no longer being maintained.
|---|---|

| Composer | [`contributte/deployer-extension`](https://packagist.org/packages/contributte/deployer-extension) |
|---|---|
| Version | ![](https://badgen.net/packagist/v/contributte/deployer-extension) |
| PHP | ![](https://badgen.net/packagist/php/contributte/deployer-extension) |
| License | ![](https://badgen.net/github/license/contributte/ftpdeployer) |

## Usage

FTP deployment extension for [Nette Framework](https://nette.org). This extension integrates [dg/ftp-deployment](https://github.com/dg/ftp-deployment) into Nette applications, providing easy configuration and deployment capabilities via FTP/FTPS protocols.

### Installation

To install this package, use [Composer](https://getcomposer.org):

```bash
composer require contributte/deployer-extension
```

### Quick Start

#### 1. Register the extension

Add the extension to your Nette configuration:

```neon
extensions:
	deployer: Contributte\Deployer\DI\DeployerExtension
```

#### 2. Configure deployment

Basic configuration example:

```neon
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

			ignore:
				- .git*
				- .idea*
				- composer.*
				- /app/config/config.local.neon
				- /log/*
				- "!/log/.htaccess"
				- /temp/*
				- "!/temp/.htaccess"
```

#### 3. Deploy your application

**Automatic deployment:**

```php
# Create Deploy Manager
$dm = $container->getByType('Contributte\Deployer\Manager');
$dm->deploy();
```

**Manual deployment:**

```php
# Create config
$config = new Config();
$config->setLogFile(..);
$config->setMode(..);

$section = new Section();
$section->setName(..);
$config->addSection($section);

# Deploy
$dm = $container->getByType('Contributte\Deployer\Manager');
$dm->manualDeploy($config);
```

### Features

#### Multiple deployment sections

Deploy to multiple servers/environments:

```neon
deployer:
	sections:
		example.com:
			remote: ftp://user:pass@example.com
			local: %wwwDir%
		test.cz:
			remote: ftp://user:pass@test.cz
			local: %wwwDir%
```

#### Listeners

Register custom listeners for pre/post deployment actions:

- **BeforeListener** - Runs before deployment
- **AfterListener** - Runs after deployment

```neon
deployer:
	sections:
		web1:
			before:
				- [@\TestBeforeListener, onBefore]
			after:
				- [@\TestAfterListener, onAfter]
```

#### Built-in Plugins

**MaintenanceListener** - Automatically manage maintenance mode during deployment:

```neon
deployer:
	plugins:
		maintenance:
			rewrite:
				- [www/index.php, www/index.maintenance]
			rename:
				- [www/.maintenance.php, www/maintenance.php]
```

**ComposerInstallListener** - Runs `composer install --no-dev --prefer-dist --optimize-autoloader` before deployment.

**ComposerUpdateListener** - Runs `composer update --no-dev --prefer-dist --optimize-autoloader` before deployment.

#### File Purging

Clean specific directories during deployment:

```neon
deployer:
	sections:
		web1:
			purge:
				- temp/cache
				- temp/myfolder
```

## Versions

| State  | Version | Branch | Nette | PHP   |
|--------|---------|--------|-------|-------|
| stable | ^3.2.1  | master | 3.0+  | >=7.2 |

## Development

This package was maintained by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for using this package.
