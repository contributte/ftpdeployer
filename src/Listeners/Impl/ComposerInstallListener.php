<?php declare(strict_types = 1);

namespace Contributte\Deployer\Listeners\Impl;

use Contributte\Deployer\Config\Config;
use Contributte\Deployer\Config\Section;
use Contributte\Deployer\Listeners\BeforeListener;
use Contributte\Deployer\Utils\System;
use Deployment\Deployer;
use Deployment\Logger;
use Deployment\Server;

class ComposerInstallListener implements BeforeListener
{

	public function onBefore(Config $config, Section $section, Server $server, Logger $logger, Deployer $deployer): void
	{
		$cwd = $section->getLocal();

		// Display info
		$logger->log(sprintf('Composer: composer install --no-dev --prefer-dist --optimize-autoloader -d %s', $cwd));

		// Execute command
		System::run(sprintf('composer install --no-dev --prefer-dist --optimize-autoloader -d %s', $cwd), $return);

		// Display result
		if ($return) {
			$logger->log('Composer: FAILED (' . $return . ')', 'red');
		} else {
			$logger->log('Composer: INSTALLED / OPTIMIZED', 'green');
		}
	}

}
