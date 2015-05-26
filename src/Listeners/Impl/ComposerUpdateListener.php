<?php

namespace Minetro\Deployer\Listeners\Impl;

use Deployment\Deployer;
use Deployment\Logger;
use Deployment\Server;
use Minetro\Deployer\Config\Config;
use Minetro\Deployer\Config\Section;
use Minetro\Deployer\Listeners\BeforeListener;
use Minetro\Deployer\Utils\System;

class ComposerUpdateListener implements BeforeListener
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
        $cwd = $section->getLocal();

        // Display info
        $logger->log(sprintf('Composer: composer update --no-dev --prefer-dist --optimize-autoloader -d %s', $cwd));

        // Execute command
        System::run(sprintf('composer update --no-dev --prefer-dist --optimize-autoloader -d %s', $cwd), $return);

        // Display result
        if ($return) {
            $logger->log('Composer: FAILED (' . $return . ')', 'red');
        } else {
            $logger->log('Composer: UPDATED / OPTIMIZED', 'green');
        }
    }
}
