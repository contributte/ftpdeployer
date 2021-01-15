<?php declare(strict_types = 1);

namespace Contributte\Deployer\Config;

class ConfigFactory
{

	/** @var object */
	private $data;

	/**
	 * @param object $data
	 */
	public function __construct(object $data)
	{
		$this->data = $data;
	}

	public function create(): Config
	{
		// Parse config
		$config = new Config();

		// Parse mode
		if (isset($this->data->config) && isset($this->data->config->mode)) {
			switch ($this->data->config->mode) {
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
		} else {
			$config->setMode(Config::MODE_TEST);
		}

		$config->setLogFile((isset($this->data->config) && isset($this->data->config->logFile)) ? $this->data->config->logFile : '');
		$config->setTempDir((isset($this->data->config) && isset($this->data->config->tempDir)) ? $this->data->config->tempDir : sys_get_temp_dir() . '/deployment');

		// Set or detect colors support
		if (isset($this->data->config) && isset($this->data->config->colors) && $this->data->config->colors !== null) {
			$config->setColors((bool) $this->data->config->colors);
		} else {
			$config->setColors(PHP_SAPI === 'cli' && ((function_exists('posix_isatty') && posix_isatty(STDOUT))
					|| getenv('ConEmuANSI') === 'ON' || getenv('ANSICON') !== false));
		}

		// Set user data
		if (isset($this->data->userdata)) {
			$config->setUserdata($this->data->userdata);
		}

		// Set plugins
		if (isset($this->data->plugins)) {
			$config->setPlugins($this->data->plugins);
		}

		// Parse sections
		if (isset($this->data->sections)) {
			foreach ($this->data->sections as $name => $sdata) {
				$section = new Section();
				$section->setName($name);
				$section->setTestMode($sdata->testMode);
				$section->setLocal($sdata->local);
				$section->setRemote($sdata->remote);
				$section->setPreprocess($sdata->preprocess);
				$section->setPreprocessMasks($sdata->preprocess !== false ? $sdata->preprocess : []);
				$section->setAllowDelete($sdata->allowdelete);
				$section->setIgnoreMasks($sdata->ignore);
				$section->setDeployFile($sdata->deployFile);
				$section->setAfterCallbacks($sdata->after);
				$section->setBeforeCallbacks($sdata->before);
				$section->setPassiveMode($sdata->passiveMode);
				$section->setPurges($sdata->purge);
				$section->setFilePermissions($sdata->filePermissions);
				$section->setDirPermissions($sdata->dirPermissions);

			// Add to config
				$config->addSection($section);
			}
		}

		return $config;
	}

}
