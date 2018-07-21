<?php declare(strict_types = 1);

namespace Contributte\Deployer\Listeners\Impl;

use Contributte\Deployer\Config\Config;
use Contributte\Deployer\Config\Section;
use Contributte\Deployer\Listeners\AfterListener;
use Contributte\Deployer\Listeners\BeforeListener;
use Contributte\Deployer\Utils\Helpers;
use Deployment\Deployer;
use Deployment\Logger;
use Deployment\Server;
use Nette\InvalidStateException;

class MaintenanceListener implements BeforeListener, AfterListener
{

	/**
	 * Plugin name
	 */
	public const PLUGIN = 'maintenance';

	/**
	 * Maintenance modes
	 */
	public const MODE_REWRITE = 'rewrite';
	public const MODE_RENAME = 'rename';

	/**
	 * Maintenance file extensions
	 */
	public const MAINTENANCE_EXTENSION = 'maintenance';

	/** @var mixed[] $defaults */
	private $defaults = [
		self::MODE_REWRITE => null,
		self::MODE_RENAME => null,
	];

	/** @var mixed[] */
	private $batch = [
		self::MODE_REWRITE => [],
		self::MODE_RENAME => [],
	];

	/** @var Server */
	private $server;

	/** @var Logger */
	private $logger;

	/** @var mixed[] */
	private $plugin;

	/** @var string */
	private $pluginName;

	public function onBefore(Config $config, Section $section, Server $server, Logger $logger, Deployer $deployer): void
	{
		if (!$this->load($config, $section, $server, $logger, $deployer)) return;

		// Run maintenance procedures ==========================================

		if (isset($this->plugin[self::MODE_REWRITE]) && is_array($this->plugin[self::MODE_REWRITE])) {
			$time = time();

			$this->log('start rewriting');
			$this->doRewrite($this->plugin[self::MODE_REWRITE]);

			$time = time() - $time;
			$this->log(sprintf('rewriting finished (in %s seconds)', $time), 'lime');
		}

		if (isset($this->plugin[self::MODE_RENAME]) && is_array($this->plugin[self::MODE_RENAME])) {
			$time = time();

			$this->log('start renaming');
			$this->doRename($this->plugin[self::MODE_RENAME]);

			$time = time() - $time;
			$this->log(sprintf('renaming finished (in %s seconds)', $time), 'lime');
		}
	}

	public function onAfter(Config $config, Section $section, Server $server, Logger $logger, Deployer $deployer): void
	{
		if (!$this->load($config, $section, $server, $logger, $deployer)) return;

		// Run maintenance procedures ==========================================

		if (isset($this->plugin[self::MODE_REWRITE]) && is_array($this->plugin[self::MODE_REWRITE])) {
			$time = time();

			$this->log('revert - start rewriting');
			$this->doRewrite($this->batch[self::MODE_REWRITE], true);

			$time = time() - $time;
			$this->log(sprintf('revert - rewriting finished (in %s seconds)', $time), 'lime');
			$this->batch[self::MODE_REWRITE] = [];
		}

		if (isset($this->plugin[self::MODE_RENAME]) && is_array($this->plugin[self::MODE_RENAME])) {
			$time = time();

			$this->log('revert - start renaming');
			$this->doRename($this->batch[self::MODE_RENAME], true);

			$time = time() - $time;
			$this->log(sprintf('revert - renaming finished (in %s seconds)', $time), 'lime');
			$this->batch[self::MODE_RENAME] = [];
		}
	}

	/**
	 * HELPERS *****************************************************************
	 */
	private function load(Config $config, Section $section, Server $server, Logger $logger, Deployer $deployer): bool
	{
		$this->server = $server;
		$this->logger = $logger;

		$plugins = $config->getPlugins();
		$this->plugin = $plugins[self::PLUGIN] ?? null;
		$this->pluginName = ucfirst(self::PLUGIN);

		// Has plugin filled config?
		if (!$this->plugin) {
			$logger->log(sprintf('%s: please fill config', $this->pluginName), 'red');

			return false;
		}

		try {
			// Validate plugin config
			Helpers::validateConfig($this->defaults, $this->plugin, self::PLUGIN);
		} catch (InvalidStateException $ex) {
			$logger->log(sprintf('%s: bad configuration (%s)', $this->pluginName, $ex->getMessage()), 'red');

			return false;
		}

		return true;
	}

	private function log(string $message, ?string $color = null): void
	{
		$this->logger->log(sprintf('%s: %s', $this->pluginName, $message), $color);
	}

	/**
	 * IMPLEMENTATION **********************************************************
	 */

	/**
	 * @param mixed[] $list
	 */
	protected function doRewrite(array $list, bool $reverse = false): void
	{
		foreach ($list as $pair) {
			// Skip invalid pair
			if (!is_array($pair) && count($pair) !== 2) continue;

			 [$file, $replaceBy] = $pair;

			if ($reverse) {
				// REVERSE MODE
				// #1 revert: replace by file
				$this->server->renameFile($file, $replaceBy);
				$this->log(sprintf('rename from [%s] to [%s]', $file, $replaceBy));

				// 2# maintenance file rename to original file
				$this->server->renameFile($file . '.' . self::MAINTENANCE_EXTENSION, $file);
				$this->log(sprintf('rename from [%s] to [%s]', $file . '.' . self::MAINTENANCE_EXTENSION, $file));
			} else {
				// NORMAL MODE
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
	 * @param mixed[] $list
	 */
	protected function doRename(array $list, bool $reverse = false): void
	{
		foreach ($list as $pair) {
			// Skip invalid pair
			if (!is_array($pair) && count($pair) !== 2) continue;

			 [$old, $new] = $pair;

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
