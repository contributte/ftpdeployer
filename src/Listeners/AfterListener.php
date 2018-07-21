<?php declare(strict_types = 1);

namespace Contributte\Deployer\Listeners;

use Contributte\Deployer\Config\Config;
use Contributte\Deployer\Config\Section;
use Deployment\Deployer;
use Deployment\Logger;
use Deployment\Server;

interface AfterListener
{

	public function onAfter(Config $config, Section $section, Server $server, Logger $logger, Deployer $deployer): void;

}
