<?php declare(strict_types = 1);

namespace Contributte\Deployer\DI;

use Contributte\Deployer\Config\Config;
use Contributte\Deployer\Config\ConfigFactory;
use Contributte\Deployer\Manager;
use Contributte\Deployer\Runner;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
final class DeployerExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'config' => Expect::structure([
				'mode' => Expect::anyOf(Config::MODE_GENERATE, Config::MODE_RUN, Config::MODE_TEST)->default(Config::MODE_TEST),
				'logFile' => Expect::string()->required(),
				'tempDir' => Expect::string()->required(),
				'colors' => Expect::bool()->default(false),
			]),
			'userdata' => Expect::mixed()->default([]),
			'plugins' => Expect::mixed()->default([]),
			'sections' => Expect::arrayOf(
				Expect::structure([
					'testMode' => Expect::bool()->default(true),
					'deployFile' => Expect::string(),
					'remote' => Expect::string(),
					'local' => Expect::string()->required(),
					'ignore' => Expect::arrayOf('string')->default([]),
					'allowdelete' => Expect::bool()->default(true),
					'before' => Expect::mixed()->default([]),
					'after' => Expect::mixed()->default([]),
					'purge' => Expect::mixed()->default([]),
					'preprocess' => Expect::bool()->default(false),
					'passiveMode' => Expect::bool()->default(false),
					'filePermissions' => Expect::string(),
					'dirPermissions' => Expect::string(),
				])->required()
			),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		$builder->addDefinition($this->prefix('manager'))
			->setFactory(Manager::class, [
				new Statement(Runner::class),
				new Statement(ConfigFactory::class, [$config]),
			]);
	}

}
