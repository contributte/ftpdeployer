<?php

namespace Minetro\Deployer\Listeners\Impl;

use Deployment\Deployer;
use Deployment\Logger;
use Deployment\Server;
use Minetro\Deployer\Config\Config;
use Minetro\Deployer\Config\Section;
use Minetro\Deployer\Listeners\AfterListener;
use Minetro\Deployer\Listeners\BeforeListener;
use Minetro\Deployer\Utils\Helpers;
use Nette\InvalidStateException;
use Nette\NotImplementedException;
use Nette\Utils\FileSystem;

class MaintenanceListener implements BeforeListener, AfterListener
{

    /** Plugin name */
    const PLUGIN = 'maintenance';

    /** Maintenance modes */
    const MODE_REWRITE = 'rewrite';
    const MODE_RENAME = 'rename';

    /** Maintenance file extensions */
    const MAINTENANCE_EXTENSION = 'maintenance';

    private $defaults = [
        self::MODE_REWRITE => NULL,
        self::MODE_RENAME => NULL,
    ];

    /** @var array */
    private $batch = [
        self::MODE_REWRITE => [],
        self::MODE_RENAME => [],
    ];

    /** @var array */
    private $plugin;

    /** @var string */
    private $pluginName;

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
            $logger->log("$pluginName: please fill config", 'red');
            return;
        }

        // Validate plugin config
        try {
            Helpers::validateConfig($this->defaults, $config, self::PLUGIN);
        } catch (InvalidStateException $ex) {
            $logger->log(sprintf("%s: bad configuration (%s)", $pluginName, $ex->getMessage()), 'red');
            return;
        }

        // Choose maintenance mode
        switch ($plugin['mode']) {
            case self::MODE_REWRITE:
                $this->doRewrite($plugin[self::MODE_REWRITE]);
                break;
            case self::MODE_RENAME:
                $this->doRename($plugin[self::MODE_REWRITE]);
                break;
            default:
                throw new InvalidStateException('Unknown mode: ' . $plugin['mode']);
        }
    }

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
        $this->load($config, $section, $server, $logger, $deployer);

        // Choose maintenance mode
        switch ($this->plugin['mode']) {
            case self::MODE_REWRITE:
                $this->doRewrite($this->batch[self::MODE_REWRITE], TRUE);
                $this->batch[self::MODE_REWRITE] = [];
                break;
            case self::MODE_RENAME:
                $this->doRename($this->batch[self::MODE_RENAME], TRUE);
                $this->batch[self::MODE_RENAME] = [];
                break;
            default:
                throw new InvalidStateException('Unknown mode: ' . $this->plugin['mode']);
        }
    }

    /**
     * HELPERS *****************************************************************
     */

    /**
     * @param Config $config
     * @param Section $section
     * @param Server $server
     * @param Logger $logger
     * @param Deployer $deployer
     * @return void
     */
    private function load(Config $config, Section $section, Server $server, Logger $logger, Deployer $deployer)
    {
        $plugins = $config->getPlugins();
        $this->plugin = isset($plugins[self::PLUGIN]) ? $plugins[self::PLUGIN] : NULL;
        $this->pluginName = ucfirst(self::PLUGIN);

        // Has plugin filled config?
        if (!$this->plugin) {
            $logger->log("{$this->pluginName}: please fill config", 'red');
            return;
        }

        try {
            // Validate plugin config
            Helpers::validateConfig($this->defaults, $config, self::PLUGIN);
        } catch (InvalidStateException $ex) {
            $logger->log(sprintf("%s: bad configuration (%)", $this->pluginName, $ex->getMessage()), 'red');
            return;
        }
    }

    /**
     * IMPLEMENTATION **********************************************************
     */

    /**
     * @param array $list
     * @param bool $reverse
     * @return void
     */
    protected function doRewrite(array $list, $reverse = FALSE)
    {
        foreach ($list as $pair) {
            // Skip invalid pair
            if (!is_array($pair) && count($pair) != 2) continue;

            list ($file, $replaceBy) = $pair;

            if (!$reverse) {
                ## REVERSE MODE
                // #1 revert: replace by file
                FileSystem::rename($file, $replaceBy);

                // 2# maintenance file rename to original file
                FileSystem::rename($file . '.' . self::MAINTENANCE_EXTENSION, $file);
            } else {
                // 1# rename to maintenance file
                FileSystem::rename($file, $file . '.' . self::MAINTENANCE_EXTENSION);

                // #2 replace by file
                FileSystem::rename($replaceBy, $file);

                // 3# store to batch
                $this->batch[self::MODE_REWRITE][] = [$file, $replaceBy];
            }
        }
    }

    /**
     * @param array $list
     * @param bool $reverse
     * @return void
     */
    protected function doRename(array $list, $reverse = FALSE)
    {
        foreach ($list as $pair) {
            // Skip invalid pair
            if (!is_array($pair) && count($pair) != 2) continue;

            list ($old, $new) = $pair;

            // 1# rename to new file
            FileSystem::rename($old, $new);

            // 2# store to batch
            if (!$reverse) {
                $this->batch[self::MODE_RENAME][] = [$new, $old];
            }
        }
    }
}
