<?php

namespace Minetro\Deployer\Listeners\Impl;

use Deployment\Deployer;
use Deployment\Logger;
use Deployment\Server;
use Minetro\Deployer\Config\Config;
use Minetro\Deployer\Config\Section;
use Minetro\Deployer\Listeners\BeforeListener;

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
        passthru(sprintf('composer update --no-dev --prefer-dist --optimize-autoloader -d %s', $cwd), $return);

        // Display result
        if ($return) {
            $logger->log('Composer failed: ' . $return, 'red');
        } else {
            $logger->log('Composer optimized', 'green');
        }
    }
}
