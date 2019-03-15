<?php declare(strict_types = 1);

namespace Contributte\Deployer\DI;

use Contributte\Deployer\Config\Config;
use Contributte\Deployer\Config\ConfigFactory;
use Contributte\Deployer\Manager;
use Contributte\Deployer\Runner;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;

/**
 * Deployer Extension
 */
final class DeployerExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'config' => [
			'mode' => Config::MODE_TEST,
			'logFile' => '%appDir/../log/deploy.log',
			'tempDir' => '%appDir/../temp',
			'colors' => null,
		],
		'sections' => [],
		'userdata' => [],
		'plugins' => [],
	];

	/** @var mixed[] */
	private $sectionDefaults = [
		'testMode' => true,
		'deployFile' => null,
		'remote' => null,
		'local' => '%appDir',
		'ignore' => [],
		'allowdelete' => true,
		'before' => [],
		'after' => [],
		'purge' => [],
		'preprocess' => false,
		'passiveMode' => false,
		'filePermissions' => '',
		'dirPermissions' => '',
	];

	/**
	 * Processes configuration data. Intended to be overridden by descendant.
	 */
	public function loadConfiguration(): void
	{
		// Validate config
		$config = $this->validateConfig($this->defaults);

		// Get builder
		$builder = $this->getContainerBuilder();

		// Process sections
		foreach ($config['sections'] as $name => $section) {

			// Validate and merge section
			$config['sections'][$name] = $this->validateConfig($this->sectionDefaults, $section);
		}

		// Add deploy manager
		$builder->addDefinition($this->prefix('manager'))
			->setFactory(Manager::class, [
				new Statement(Runner::class),
				new Statement(ConfigFactory::class, [$config]),
			]);
	}

}
