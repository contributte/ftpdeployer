<?php

use Deployment\Deployer;
use Deployment\Logger;
use Deployment\Server;
use Minetro\Deployer\Listeners\AfterListener;

class TestAfterListener implements AfterListener
{

    /**
     * @param Server $server
     * @param Logger $logger
     * @param Deployer $deployer
     * @return void
     */
    public function onAfter(Server $server, Logger $logger, Deployer $deployer)
    {
        $stop();
    }

}