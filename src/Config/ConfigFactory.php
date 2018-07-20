<?php declare(strict_types = 1);

namespace Contributte\Deployer\Config;

class ConfigFactory
{

	/** @var array */
	private $data;

	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function create(): Config
	{
		// Parse config
		$config = new Config();

		// Parse mode
		switch ($this->data['config']['mode']) {
			case Config::MODE_GENERATE:
				$mode = Config::MODE_GENERATE;
				break;
			case Config::MODE_RUN:
				$mode = Config::MODE_RUN;
				break;
			default:
				$mode = Config::MODE_TEST;
		}

		// Set mode (run|generate|test)
		$config->setMode($mode);
		$config->setLogFile($this->data['config']['logFile']);
		$config->setTempDir($this->data['config']['tempDir'] ?? sys_get_temp_dir() . '/deployment');

		// Set or detect colors support
		if ($this->data['config']['colors'] !== null) {
			$config->setColors((bool) $this->data['config']['colors']);
		} else {
			$config->setColors(PHP_SAPI === 'cli' && ((function_exists('posix_isatty') && posix_isatty(STDOUT))
					|| getenv('ConEmuANSI') === 'ON' || getenv('ANSICON') !== false));
		}

		// Set user data
		$config->setUserdata($this->data['userdata']);

		// Set plugins
		$config->setPlugins($this->data['plugins']);

		// Parse sections
		foreach ($this->data['sections'] as $name => $sdata) {
			$section = new Section();
			$section->setName($name);
			$section->setTestMode($sdata['testMode']);
			$section->setLocal($sdata['local']);
			$section->setRemote($sdata['remote']);
			$section->setPreprocess($sdata['preprocess']);
			$section->setPreprocessMasks($sdata['preprocess'] !== false ? $sdata['preprocess'] : []);
			$section->setAllowDelete($sdata['allowdelete']);
			$section->setIgnoreMasks($sdata['ignore']);
			$section->setDeployFile($sdata['deployFile']);
			$section->setAfterCallbacks($sdata['after']);
			$section->setBeforeCallbacks($sdata['before']);
			$section->setPassiveMode($sdata['passiveMode']);
			$section->setPurges($sdata['purge']);

			// Add to config
			$config->addSection($section);
		}

		return $config;
	}

}
