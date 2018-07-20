<?php declare(strict_types = 1);

namespace Contributte\Deployer\Config;

class Config
{
	/** Modes */
	public const MODE_GENERATE = 'generate';
	public const MODE_TEST = 'test';
	public const MODE_RUN = 'deploy';

	/** @var string */
	private $mode = self::MODE_RUN;

	/** @var string */
	private $logFile;

	/** @var string */
	private $tempDir;

	/** @var bool */
	private $colors;

	/** @var Section */
	private $sections = [];

	/** @var mixed[] */
	private $userdata = [];

	/** @var mixed[] */
	private $plugins = [];

	public function getMode(): ?string
	{
		return $this->mode;
	}

	public function setMode(string $mode): void
	{
		$this->mode = $mode;
	}

	public function getLogFile(): ?string
	{
		return $this->logFile;
	}

	public function setLogFile(string $logFile): void
	{
		$this->logFile = $logFile;
	}

	public function useColors(): ?bool
	{
		return $this->colors;
	}

	public function setColors(bool $colors): void
	{
		$this->colors = (bool) $colors;
	}

	public function getTempDir(): ?string
	{
		return $this->tempDir;
	}

	public function setTempDir(string $tempDir): void
	{
		$this->tempDir = $tempDir;
	}

	/**
	 * @return Section[]
	 */
	public function getSections(): array
	{
		return $this->sections;
	}

	public function setSections(Section $sections): void
	{
		$this->sections = $sections;
	}

	public function addSection(Section $section): void
	{
		$this->sections[] = $section;
	}

	/**
	 * @return mixed[]
	 */
	public function getUserdata(): array
	{
		return $this->userdata;
	}

	/**
	 * @param mixed[] $userdata
	 */
	public function setUserdata(array $userdata): void
	{
		$this->userdata = $userdata;
	}

	/**
	 * @param string $key
	 * @param mixed $data
	 */
	public function addUserdata(string $key, $data): void
	{
		$this->userdata[$key] = $data;
	}

	/**
	 * @return mixed[]
	 */
	public function getPlugins(): array
	{
		return $this->plugins;
	}

	/**
	 * @param mixed[] $plugins
	 */
	public function setPlugins(array $plugins): void
	{
		$this->plugins = $plugins;
	}

	/**
	 * @param string $name
	 * @param mixed[] $data
	 */
	public function addPlugin(string $name, array $data): void
	{
		$this->plugins[$name] = $data;
	}

}
