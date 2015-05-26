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

    /** @var Server */
    private $server;

    /** @var Logger */
    private $logger;

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
        if (!$this->load($config, $section, $server, $logger, $deployer)) return;

        // Run maintenance procedures ==========================================

        if (isset($this->plugin[self::MODE_REWRITE]) && is_array($this->plugin[self::MODE_REWRITE])) {
            $time = time();

            $this->log('start rewriting');
            $this->doRewrite($this->plugin[self::MODE_REWRITE]);

            $time = time() - $time;
            $this->log("rewriting finished (in $time seconds)", 'lime');
        }

        if (isset($this->plugin[self::MODE_RENAME]) && is_array($this->plugin[self::MODE_RENAME])) {
            $time = time();

            $this->log('start renaming');
            $this->doRename($this->plugin[self::MODE_RENAME]);

            $time = time() - $time;
            $this->log("renaming finished (in $time seconds)", 'lime');
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
        if (!$this->load($config, $section, $server, $logger, $deployer)) return;

        // Run maintenance procedures ==========================================

        if (isset($this->plugin[self::MODE_REWRITE]) && is_array($this->plugin[self::MODE_REWRITE])) {
            $time = time();

            $this->log('revert - start rewriting');
            $this->doRewrite($this->batch[self::MODE_REWRITE], TRUE);

            $time = time() - $time;
            $this->log("revert - rewriting finished (in $time seconds)", 'lime');
            $this->batch[self::MODE_REWRITE] = [];
        }

        if (isset($this->plugin[self::MODE_RENAME]) && is_array($this->plugin[self::MODE_RENAME])) {
            $time = time();

            $this->log('revert - start renaming');
            $this->doRename($this->batch[self::MODE_RENAME], TRUE);

            $time = time() - $time;
            $this->log("revert - renaming finished (in $time seconds)", 'lime');
            $this->batch[self::MODE_RENAME] = [];
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
     * @return bool
     */
    private function load(Config $config, Section $section, Server $server, Logger $logger, Deployer $deployer)
    {
        $this->server = $server;
        $this->logger = $logger;

        $plugins = $config->getPlugins();
        $this->plugin = isset($plugins[self::PLUGIN]) ? $plugins[self::PLUGIN] : NULL;
        $this->pluginName = ucfirst(self::PLUGIN);

        // Has plugin filled config?
        if (!$this->plugin) {
            $logger->log("{$this->pluginName}: please fill config", 'red');
            return FALSE;
        }

        try {
            // Validate plugin config
            Helpers::validateConfig($this->defaults, $this->plugin, self::PLUGIN);
        } catch (InvalidStateException $ex) {
            $logger->log(sprintf("%s: bad configuration (%s)", $this->pluginName, $ex->getMessage()), 'red');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * @param string $message
     * @param string $color
     */
    private function log($message, $color = NULL)
    {
        $this->logger->log("{$this->pluginName}: $message", $color);
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

            if ($reverse) {
                ## REVERSE MODE
                // #1 revert: replace by file
                $this->server->renameFile($file, $replaceBy);
                $this->log(sprintf('rename from [%s] to [%s]', $file, $replaceBy));

                // 2# maintenance file rename to original file
                $this->server->renameFile($file . '.' . self::MAINTENANCE_EXTENSION, $file);
                $this->log(sprintf('rename from [%s] to [%s]', $file . '.' . self::MAINTENANCE_EXTENSION, $file));
            } else {
                ## NORMAL MODE
                // 1# rename to maintenance file
                $this->server->renameFile($file, $file . '.' . self::MAINTENANCE_EXTENSION);
                $this->log(sprintf('rename from [%s] to [%s]', $file, $file . '.' . self::MAINTENANCE_EXTENSION));

                // #2 replace by file
                $this->server->renameFile($replaceBy, $file);
                $this->log(sprintf('rename from [%s] to [%s]', $replaceBy, $file));

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
            $this->server->renameFile($old, $new);
            $this->log(sprintf('rename from [%s] to [%s]', $old, $new));

            if (!$reverse) {
                // 2# store to batch
                $this->batch[self::MODE_RENAME][] = [$new, $old];
            }
        }
    }
}
