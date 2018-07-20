<?php declare(strict_types = 1);

namespace Contributte\Deployer;

use Contributte\Deployer\Config\Config;
use Contributte\Deployer\Config\ConfigFactory;

class Manager
{

	/** @var Runner */
	private $runner;

	/** @var Config */
	private $config;

	public function __construct(Runner $runner, ConfigFactory $config)
	{
		$this->runner = $runner;
		$this->config = $config->create();
	}

	/**
	 * Run automatic deploy
	 */
	public function deploy(): void
	{
		$this->runner->run($this->config);
	}

	/**
	 * Run deploy by given config
	 */
	public function manualDeploy(Config $config): void
	{
		$this->runner->run($config);
	}

}
