<?php

namespace Minetro\Deployer;

use Minetro\Deployer\Config\Config;
use Minetro\Deployer\Config\ConfigFactory;

class Manager
{

    /** @var Runner */
    private $runner;

    /** @var Config */
    private $config;

    /**
     * @param Runner $runner
     * @param ConfigFactory $config
     */
    public function __construct(Runner $runner, ConfigFactory $config)
    {
        $this->runner = $runner;
        $this->config = $config->create();
    }

    /**
     * Run automatic deploy
     */
    public function deploy()
    {
        $this->runner->run($this->config);
    }

    /**
     * Run deploy by given config
     *
     * @param Config $config
     */
    public function manualDeploy(Config $config)
    {
        $this->runner->run($config);
    }

}
