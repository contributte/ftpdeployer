<?php

/**
 * @copyright (c) 2015 Milan Sulc
 * @copyright (c) 2009 David Grudl (http://davidgrudl.com) All rights reserved.
 */

namespace Minetro\Deployer;

use Deployment\Deployer;
use Deployment\FtpServer;
use Deployment\Logger;
use Deployment\Preprocessor;
use Deployment\SshServer;
use Minetro\Deployer\Config\Config;
use Minetro\Deployer\Config\Section;
use Minetro\Deployer\Exceptions\DeployException;

/**
 * Class Runner
 *
 */
class Runner
{

    /** @var Logger */
    private $logger;

    /**
     * @param Config $config
     */
    public function run(Config $config)
    {
        // Create logger
        $this->logger = new Logger($config->getLogFile());
        $this->logger->useColors = $config->useColors();

        // Create temp dir
        if (!is_dir($tempDir = $config->getTempDir())) {
            $this->logger->log("Creating temporary directory $tempDir");
            @mkdir($tempDir, 0777, TRUE);
        }

        // Start time
        $time = time();
        $this->logger->log("Started at " . date('[Y/m/d H:i]'));

        // Show info
        $this->logger->log(sprintf('Found sections: %d (%s)', count($config->getSections()), implode(',', array_keys($config->getSections()))));

        // Process all sections
        foreach ($config->getSections() as $section) {
            // Show info
            $this->logger->log("\nDeploying section [{$section->getName()}]");

            // Create deployer
            $deployment = $this->createDeployer($config, $section);
            $deployment->tempDir = $tempDir;

            // Detect mode -> generate
            if ($config->getMode() === 'generate') {
                $this->logger->log('Scanning files');
                $localFiles = $deployment->collectFiles();
                $this->logger->log("Saved " . $deployment->writeDeploymentFile($localFiles));
                continue;
            }

            // Show info
            if ($deployment->testMode) {
                $this->logger->log('Test mode');
            }
            if (!$deployment->allowDelete) {
                $this->logger->log('Deleting disabled');
            }

            // Deploy
            $deployment->deploy();
        }

        // Show elapsed time
        $time = time() - $time;
        $this->logger->log("\nFinished at " . date('[Y/m/d H:i]') . " (in $time seconds)", 'lime');
    }

    /**
     * @param Section $section
     * @return Deployer
     * @throws DeployException
     */
    public function createDeployer(Config $config, Section $section)
    {
        // Validate section remote
        if (!parse_url($section->getRemote())) {
            throw new DeployException("Missing or invalid 'remote' URL in config.");
        }

        // Create *Server
        $server = parse_url($section->getRemote(), PHP_URL_SCHEME) === 'sftp'
            ? new SshServer($section->getRemote())
            : new FtpServer($section->getRemote(), $section->isPassiveMode());

        // Create deployer
        $deployment = new Deployer($server, $section->getLocal(), $this->logger);

        // Set-up preprocessing
        if ($section->isPreprocess()) {
            $deployment->preprocessMasks = empty($section->getPreprocessMasks()) ? ['*.js', '*.css'] : $section->getPreprocessMasks();
            $preprocessor = new Preprocessor($this->logger);
            $deployment->addFilter('js', [$preprocessor, 'expandApacheImports']);
            $deployment->addFilter('js', [$preprocessor, 'compress'], TRUE);
            $deployment->addFilter('css', [$preprocessor, 'expandApacheImports']);
            $deployment->addFilter('css', [$preprocessor, 'expandCssImports']);
            $deployment->addFilter('css', [$preprocessor, 'compress'], TRUE);
        }

        // Merge ignore masks
        $deployment->ignoreMasks = array_merge(
            ['*.bak', '.svn', '.git*', 'Thumbs.db', '.DS_Store'],
            $section->getIgnoreMasks()
        );

        // Basic settings
        $deployment->deploymentFile = empty($section->getDeployFile()) ? $deployment->deploymentFile : $section->getDeployFile();
        $deployment->allowDelete = $section->isAllowDelete();
        $deployment->toPurge = $section->getPurges();
        $deployment->testMode = $section->isTestMode();

        // Before callbacks
        $bc = [NULL, NULL];
        foreach ($section->getBeforeCallbacks() as $cb) {
            $bc[is_callable($cb)][] = $cb;
        }
        $deployment->runBefore = $bc[0];
        $deployment->runBefore[] = function ($server, $logger, $deployer) use ($bc, $config, $section) {
            foreach ((array)$bc[1] as $c) {
                call_user_func_array($c, [$config, $section, $server, $logger, $deployer]);
            }
        };

        // After callbacks
        $ac = [NULL, NULL];
        foreach ($section->getAfterCallbacks() as $cb) {
            $ac[is_callable($cb)][] = $cb;
        }
        $deployment->runAfter = $ac[0];
        $deployment->runAfter[] = function ($server, $logger, $deployer) use ($ac, $config, $section) {
            foreach ((array)$ac[1] as $c) {
                call_user_func_array($c, [$config, $section, $server, $logger, $deployer]);
            }
        };

        return $deployment;
    }
}
