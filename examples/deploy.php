<?php

require __DIR__ . '/vendor/autoload.php';

# Configurator
$configurator = new Nette\Configurator;
$configurator->setDebugMode(TRUE);
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

# Configs
$configurator->addConfig(__DIR__ . '/config/config.neon');

# Create DI Container
$container = $configurator->createContainer();

# Create Deploy Manager
$dm = $container->getByType('Minetro\Deployer\Manager');
$dm->deploy();
