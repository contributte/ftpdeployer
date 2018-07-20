<?php declare(strict_types = 1);

namespace Contributte\Deployer\Config;

use Contributte\Deployer\Listeners\AfterListener;
use Contributte\Deployer\Listeners\BeforeListener;

class Section
{

	/** @var string */
	private $name;

	/** @var string */
	private $deployFile;

	/** @var bool */
	private $testMode;

	/** @var string */
	private $remote;

	/** @var string */
	private $local;

	/** @var array */
	private $ignoreMasks = [];

	/** @var bool */
	private $allowDelete;

	/** @var BeforeListener[] */
	private $beforeCallbacks = [];

	/** @var AfterListener[] */
	private $afterCallbacks = [];

	/** @var array */
	private $purges = [];

	/** @var bool */
	private $preprocess;

	/** @var array */
	private $preprocessMasks = [];

	/** @var bool */
	private $passiveMode;

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
	 * @return array
	 */
	public function getIgnoreMasks(): array
	{
		return $this->ignoreMasks;
	}

	/**
	 * @param array $masks
	 */
	public function setIgnoreMasks(array $masks): void
	{
		$this->ignoreMasks = $masks;
	}

	public function addIgnoreMask($mask): void
	{
		$this->ignoreMasks[] = $mask;
	}

	public function isAllowDelete(): ?bool
	{
		return $this->allowDelete;
	}

	public function setAllowDelete(bool $allow): void
	{
		$this->allowDelete = $allow;
	}

	/**
	 * @return BeforeListener[]|array
	 */
	public function getBeforeCallbacks(): array
	{
		return $this->beforeCallbacks;
	}

	/**
	 * @param BeforeListener[] $callbacks
	 */
	public function setBeforeCallbacks($callbacks): void
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
	public function setAfterCallbacks($callbacks): void
	{
		$this->afterCallbacks = $callbacks;
	}

	public function addAfterCallbacks(AfterListener $callback): void
	{
		$this->afterCallbacks[] = $callback;
	}

	/**
	 * @return array
	 */
	public function getPurges(): array
	{
		return $this->purges;
	}

	/**
	 * @param array $purges
	 */
	public function setPurges(array $purges): void
	{
		$this->purges = $purges;
	}

	public function addPurge($purge): void
	{
		$this->purges[] = $purge;
	}

	public function isPreprocess(): ?bool
	{
		return $this->preprocess;
	}

	public function setPreprocess(bool $preprocess): void
	{
		$this->preprocess = (bool) $preprocess;
	}

	/**
	 * @return array
	 */
	public function getPreprocessMasks(): array
	{
		return $this->preprocessMasks;
	}

	/**
	 * @param array $masks
	 */
	public function setPreprocessMasks(array $masks): void
	{
		$this->preprocessMasks = $masks;
	}

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
		$this->passiveMode = (bool) $mode;
	}

	public function getDeployFile(): ?string
	{
		return $this->deployFile;
	}

	public function setDeployFile(string $file): void
	{
		$this->deployFile = $file;
	}

	public function isTestMode(): ?bool
	{
		return $this->testMode;
	}

	public function setTestMode(bool $testMode): void
	{
		$this->testMode = (bool) $testMode;
	}

}
