<?php declare(strict_types = 1);

namespace Contributte\Deployer;

use Contributte\Deployer\Config\Config;
use Contributte\Deployer\Config\Section;
use Contributte\Deployer\Exceptions\DeployException;
use Contributte\Deployer\Logging\StdOutLogger;
use Deployment\Deployer;
use Deployment\FtpServer;
use Deployment\Logger;
use Deployment\Preprocessor;
use Deployment\Server;
use Deployment\SshServer;

class Runner
{

	/** @var Logger */
	private $logger;

	public function run(Config $config): void
	{
		// Create logger
		$logFile = $config->getLogFile();
		$this->logger = $logFile ? new Logger($logFile) : new StdOutLogger();
		$this->logger->useColors = $config->useColors();

		// Create temp dir
		if (!is_dir($tempDir = $config->getTempDir())) {
			$this->logger->log("Creating temporary directory $tempDir");
			@mkdir($tempDir, 0777, true);
		}

		// Start time
		$time = time();
		$this->logger->log('Started at ' . date('[Y/m/d H:i]'));

		// Get sections and get sections names
		$sections = $config->getSections();
		$sectionNames = array_map(function (Section $s) {
			return $s->getName();
		}, $sections);

		// Show info
		$this->logger->log(sprintf('Found sections: %d (%s)', count($sectionNames), implode(',', $sectionNames)));

		// Process all sections
		foreach ($sections as $section) {
			// Show info
			$this->logger->log("\nDeploying section [{$section->getName()}]");

			// Create deployer
			$deployment = $this->createDeployer($config, $section);
			$deployment->tempDir = $tempDir;

			// Detect mode -> generate
			if ($config->getMode() === 'generate') {
				$this->logger->log('Scanning files');
				$localFiles = $deployment->collectPaths();
				$this->logger->log('Saved ' . $deployment->writeDeploymentFile($localFiles));
				continue;
			}

			// Show info
			if ($deployment->testMode) {
				$this->logger->log('Test mode');
			} else {
				$this->logger->log('Live mode');
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
	 * @throws DeployException
	 */
	public function createDeployer(Config $config, Section $section): Deployer
	{
		// Validate section remote
		if (!parse_url($section->getRemote())) {
			throw new DeployException("Missing or invalid 'remote' URL in config.");
		}

		// Create *Server
		$server = $this->createServer($section);

		// Create deployer
		$deployment = new Deployer($server, $section->getLocal(), $this->logger);

		// Set-up preprocessing
		if ($section->isPreprocess()) {
			$masks = $section->getPreprocessMasks();
			$deployment->preprocessMasks = empty($masks) ? ['*.js', '*.css'] : $masks;
			$preprocessor = new Preprocessor($this->logger);
			$deployment->addFilter('js', [$preprocessor, 'expandApacheImports']);
			$deployment->addFilter('js', [$preprocessor, 'compress'], true);
			$deployment->addFilter('css', [$preprocessor, 'expandApacheImports']);
			$deployment->addFilter('css', [$preprocessor, 'expandCssImports']);
			$deployment->addFilter('css', [$preprocessor, 'compress'], true);
		}

		// Merge ignore masks
		$deployment->ignoreMasks = array_merge(
			['*.bak', '.svn', '.git*', 'Thumbs.db', '.DS_Store', '.idea'],
			$section->getIgnoreMasks()
		);

		// Basic settings
		$deployFile = $section->getDeployFile();
		$deployment->deploymentFile = empty($deployFile) ? $deployment->deploymentFile : $deployFile;
		$deployment->allowDelete = $section->isAllowDelete();
		$deployment->toPurge = $section->getPurges();
		$deployment->testMode = $section->isTestMode();

		// Before callbacks
		$bc = [null, null];
		foreach ($section->getBeforeCallbacks() as $cb) {
			$bc[is_callable($cb)][] = $cb;
		}
		$deployment->runBefore = $bc[0];
		$deployment->runBefore[] = function ($server, $logger, $deployer) use ($bc, $config, $section): void {
			foreach ((array) $bc[1] as $c) {
				call_user_func_array($c, [$config, $section, $server, $logger, $deployer]);
			}
		};

		// After callbacks
		$ac = [null, null];
		foreach ($section->getAfterCallbacks() as $cb) {
			$ac[is_callable($cb)][] = $cb;
		}
		$deployment->runAfter = $ac[0];
		$deployment->runAfter[] = function ($server, $logger, $deployer) use ($ac, $config, $section): void {
			foreach ((array) $ac[1] as $c) {
				call_user_func_array($c, [$config, $section, $server, $logger, $deployer]);
			}
		};

		return $deployment;
	}

	protected function createServer(Section $section): Server
	{
		return parse_url($section->getRemote(), PHP_URL_SCHEME) === 'sftp'
			? new SshServer($section->getRemote())
			: new FtpServer($section->getRemote(), $section->isPassiveMode());
	}

}
