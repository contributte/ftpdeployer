<?php

namespace Minetro\Deployer\Listeners\Impl;

use Deployment\Deployer;
use Deployment\Logger;
use Deployment\Server;
use Minetro\Deployer\Config\Config;
use Minetro\Deployer\Config\Section;
use Minetro\Deployer\Listeners\BeforeListener;
use Minetro\Deployer\Utils\Helpers;
use Nette\InvalidStateException;
use Nette\NotImplementedException;

class MaintenanceListener implements BeforeListener
{

    /** Plugin name */
    const PLUGIN = 'maintenance';

    /** Maintenance modes */
    const MODE_REPLACE = 'replace';
    const MODE_REWRITE = 'rewrite';

    private $defaults = [
        'mode' => self::MODE_REPLACE
    ];

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
        $plugins = $config->getPlugins();
        $plugin = isset($plugins[self::PLUGIN]) ? $plugins[self::PLUGIN] : NULL;
        $pluginName = ucfirst(self::PLUGIN);

        // Has plugin filled config?
        if (!$plugin) {
            $logger->log("$pluginName: please fill config");
            return;
        }

        // Validate plugin config
        try {
            Helpers::validateConfig($this->defaults, $config, self::PLUGIN);
        } catch (InvalidStateException $ex) {
            $logger->log(sprintf("%s: bad configuration (%)", $pluginName, $ex->getMessage()));
        }

        // Choose maintenance mode
        switch ($plugin['mode']) {
            case self::MODE_REPLACE:
                throw new NotImplementedException();
            case self::MODE_REWRITE:
                throw new NotImplementedException();
            default:
                throw new InvalidStateException('Unknown mode: ' . $plugin['mode']);
        }
    }
}
