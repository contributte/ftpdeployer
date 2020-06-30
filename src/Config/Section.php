<?php declare(strict_types = 1);

namespace Contributte\Deployer\Config;

use Contributte\Deployer\Listeners\AfterListener;
use Contributte\Deployer\Listeners\BeforeListener;

class Section
{

	/** @var string|null */
	private $name;

	/** @var string|null */
	private $deployFile;

	/** @var bool */
	private $testMode = false;

	/** @var string|null */
	private $remote;

	/** @var string|null */
	private $local;

	/** @var string[] */
	private $ignoreMasks = [];

	/** @var bool */
	private $allowDelete = false;

	/** @var BeforeListener[] */
	private $beforeCallbacks = [];

	/** @var AfterListener[] */
	private $afterCallbacks = [];

	/** @var string[] */
	private $purges = [];

	/** @var bool */
	private $preprocess = false;

	/** @var mixed[] */
	private $preprocessMasks = [];

	/** @var bool */
	private $passiveMode = true;

	/** @var string */
	private $filePermissions = '';

	/** @var string */
	private $dirPermissions = '';

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getRemote(): ?string
	{
		return $this->remote;
	}

	public function setRemote(string $remote): void
	{
		$this->remote = $remote;
	}

	public function getLocal(): ?string
	{
		return $this->local;
	}

	public function setLocal(string $local): void
	{
		$this->local = $local;
	}

	/**
	 * @return mixed[]
	 */
	public function getIgnoreMasks(): array
	{
		return $this->ignoreMasks;
	}

	/**
	 * @param mixed[] $masks
	 */
	public function setIgnoreMasks(array $masks): void
	{
		$this->ignoreMasks = $masks;
	}

	/**
	 * @param mixed $mask
	 */
	public function addIgnoreMask($mask): void
	{
		$this->ignoreMasks[] = $mask;
	}

	public function isAllowDelete(): bool
	{
		return $this->allowDelete;
	}

	public function setAllowDelete(bool $allow): void
	{
		$this->allowDelete = $allow;
	}

	/**
	 * @return BeforeListener[]
	 */
	public function getBeforeCallbacks(): array
	{
		return $this->beforeCallbacks;
	}

	/**
	 * @param BeforeListener[] $callbacks
	 */
	public function setBeforeCallbacks(array $callbacks): void
	{
		$this->beforeCallbacks = $callbacks;
	}

	public function addBeforeCallbacks(BeforeListener $callback): void
	{
		$this->beforeCallbacks[] = $callback;
	}

	/**
	 * @return AfterListener[]|array
	 */
	public function getAfterCallbacks(): array
	{
		return $this->afterCallbacks;
	}

	/**
	 * @param AfterListener[] $callbacks
	 */
	public function setAfterCallbacks(array $callbacks): void
	{
		$this->afterCallbacks = $callbacks;
	}

	public function addAfterCallbacks(AfterListener $callback): void
	{
		$this->afterCallbacks[] = $callback;
	}

	/**
	 * @return mixed[]
	 */
	public function getPurges(): array
	{
		return $this->purges;
	}

	/**
	 * @param mixed[] $purges
	 */
	public function setPurges(array $purges): void
	{
		$this->purges = $purges;
	}

	/**
	 * @param mixed $purge
	 */
	public function addPurge($purge): void
	{
		$this->purges[] = $purge;
	}

	public function isPreprocess(): bool
	{
		return $this->preprocess;
	}

	public function setPreprocess(bool $preprocess): void
	{
		$this->preprocess = $preprocess;
	}

	/**
	 * @return mixed[]
	 */
	public function getPreprocessMasks(): array
	{
		return $this->preprocessMasks;
	}

	/**
	 * @param mixed[] $masks
	 */
	public function setPreprocessMasks(array $masks): void
	{
		$this->preprocessMasks = $masks;
	}

	/**
	 * @param mixed $mask
	 */
	public function addPreprocessMask($mask): void
	{
		$this->preprocessMasks[] = $mask;
	}

	public function isPassiveMode(): bool
	{
		return $this->passiveMode;
	}

	public function setPassiveMode(bool $mode): void
	{
		$this->passiveMode = $mode;
	}

	public function getDeployFile(): ?string
	{
		return $this->deployFile;
	}

	public function setDeployFile(string $file): void
	{
		$this->deployFile = $file;
	}

	public function isTestMode(): bool
	{
		return $this->testMode;
	}

	public function setTestMode(bool $testMode): void
	{
		$this->testMode = $testMode;
	}

	public function getFilePermissions(): ?int
	{
		return $this->filePermissions === '' ? null : octdec($this->filePermissions);
	}

	public function setFilePermissions(string $mask): void
	{
		$this->filePermissions = $mask;
	}

	public function getDirPermissions(): ?int
	{
		return $this->dirPermissions === '' ? null : octdec($this->dirPermissions);
	}

	public function setDirPermissions(string $mask): void
	{
		$this->dirPermissions = $mask;
	}

}
