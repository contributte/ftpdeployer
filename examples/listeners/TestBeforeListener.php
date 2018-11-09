<?php

use Deployment\Deployer;
use Deployment\Logger;
use Deployment\Server;
use Contributte\Deployer\Config\Config;
use Contributte\Deployer\Config\Section;
use Contributte\Deployer\Listeners\BeforeListener;

class TestBeforeListener implements BeforeListener
{

    /**
     * @param Config $config
     * @param Section $section
     * @param Server $server
     * @param Logger $logger
     * @param Deployer $deployer
     * @return void
     */
    public function onBefore(Config $config, Section $section, Server $server, Logger $logger, Deployer $deployer)
    {
        $stop();
    }

}
