<?php

use Deployment\Deployer;
use Deployment\Logger;
use Deployment\Server;
use Minetro\Deployer\Listeners\BeforeListener;

class TestBeforeListener implements BeforeListener
{

    /**
     * @param Server $server
     * @param Logger $logger
     * @param Deployer $deployer
     * @return void
     */
    public function onBefore(Server $server, Logger $logger, Deployer $deployer)
    {
        $stop();
    }

}