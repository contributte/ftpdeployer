<?php

use Deployment\Deployer;
use Deployment\Logger;
use Deployment\Server;
use Minetro\Deployer\Config\Config;
use Minetro\Deployer\Config\Section;
use Minetro\Deployer\Listeners\AfterListener;

class TestAfterListener implements AfterListener
{

    /**
     * @param Config $config
     * @param Section $section
     * @param Server $server
     * @param Logger $logger
     * @param Deployer $deployer
     * @return void
     */
    public function onAfter(Config $config, Section $section, Server $server, Logger $logger, Deployer $deployer)
    {
        $stop();
    }

}
