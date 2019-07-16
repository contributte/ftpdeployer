<?php declare(strict_types = 1);

namespace Contributte\Deployer\DI;

use Contributte\Deployer\Config\Config;
use Contributte\Deployer\Config\ConfigFactory;
use Contributte\Deployer\Manager;
use Contributte\Deployer\Runner;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\Schema;

/**
 * Deployer Extension
 */
final class DeployerExtension extends CompilerExtension
{

    public function getConfigSchema() : Schema
    {
        return Expect::structure(
            [
            'config' => Expect::structure(
                [
                'mode' => Expect::string(Config::MODE_TEST),
                'logFile' => Expect::string('%appDir/../log/deploy.log'),
                'tempDir' => Expect::string('%appDir/../temp'),
                'colors' => Expect::bool(),
                ]
            ),
            'sections' => Expect::array(),
            'userdata' => Expect::array(),
            'plugins' => Expect::array()
            ]
        );
    }

    /**
     * Validates section config
     *
     * @param array $data Config array of section
     *
     * @return array
     */
    public function validateSectionConfig(array $data): array
    {
        $schema = Expect::structure(
            [
            'remote' => Expect::string()->required(),
            'local' => Expect::string()->required(),
            'deployFile' => Expect::string('.dep'),
            'ignore' => Expect::array(),
            'purge' => Expect::array(),
            'after' => Expect::array(),
            'before' => Expect::array(),
            'testMode' => Expect::bool(false),
            'preprocess' => Expect::bool(false),
            'allowdelete' => Expect::bool(true),
            'passiveMode' => Expect::bool(false),
            'filePermissions' => Expect::string(''),
            'dirPermissions' => Expect::string(''),
            ]
        );

        $processor = new Processor();
        return (array) $processor->process($schema, $data);
    }

    /**
     * Processes configuration data. Intended to be overridden by descendant.
     *
     * @return void
     */
    public function loadConfiguration(): void
    {
        // Validate config
        $config =  (array) $this->config;
        $config['config'] = (array) $this->config->config;

        // Get builder
        $builder = $this->getContainerBuilder();

        // Process sections
        foreach ($config['sections'] as $name => $section) {

            // Validate and merge section
            $config['sections'][$name] = $this->validateSectionConfig($section);
        }

        // Add deploy manager
        $builder->addDefinition($this->prefix('manager'))
            ->setFactory(
                Manager::class, [
                    new Statement(Runner::class),
                    new Statement(ConfigFactory::class, [$config]),
                    ]
            );
    }

}
