<?php

namespace Minetro\Deployer\Listeners;

use Deployment\Deployer;
use Deployment\Logger;
use Deployment\Server;
use Minetro\Deployer\Config\Config;
use Minetro\Deployer\Config\Section;

interface AfterListener
{

    /**
     * @param Config $config
     * @param Section $section
     * @param Server $server
     * @param Logger $logger
     * @param Deployer $deployer
     * @return void
     */
    function onAfter(Config $config, Section $section, Server $server, Logger $logger, Deployer $deployer);

}
