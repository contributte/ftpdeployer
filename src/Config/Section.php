<?php

namespace Minetro\Deployer\Config;

use Minetro\Deployer\Listeners\AfterListener;
use Minetro\Deployer\Listeners\BeforeListener;

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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRemote()
    {
        return $this->remote;
    }

    /**
     * @param string $remote
     */
    public function setRemote($remote)
    {
        $this->remote = $remote;
    }

    /**
     * @return string
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * @param string $local
     */
    public function setLocal($local)
    {
        $this->local = $local;
    }

    /**
     * @return array
     */
    public function getIgnoreMasks()
    {
        return $this->ignoreMasks;
    }

    /**
     * @param array $masks
     */
    public function setIgnoreMasks($masks)
    {
        $this->ignoreMasks = $masks;
    }

    /**
     * @param $mask
     */
    public function addIgnoreMask($mask)
    {
        $this->ignoreMasks[] = $mask;
    }

    /**
     * @return boolean
     */
    public function isAllowDelete()
    {
        return $this->allowDelete;
    }

    /**
     * @param boolean $allow
     */
    public function setAllowDelete($allow)
    {
        $this->allowDelete = $allow;
    }

    /**
     * @return BeforeListener[]|array
     */
    public function getBeforeCallbacks()
    {
        return $this->beforeCallbacks;
    }

    /**
     * @param BeforeListener[] $callbacks
     */
    public function setBeforeCallbacks($callbacks)
    {
        $this->beforeCallbacks = $callbacks;
    }

    /**
     * @param BeforeListener
     */
    public function addBeforeCallbacks($callback)
    {
        $this->beforeCallbacks[] = $callback;
    }

    /**
     * @return AfterListener[]|array
     */
    public function getAfterCallbacks()
    {
        return $this->afterCallbacks;
    }

    /**
     * @param AfterListener[] $callbacks
     */
    public function setAfterCallbacks($callbacks)
    {
        $this->afterCallbacks = $callbacks;
    }

    /**
     * @param AfterListener
     */
    public function addAfterCallbacks($callback)
    {
        $this->afterCallbacks[] = $callback;
    }

    /**
     * @return array
     */
    public function getPurges()
    {
        return $this->purges;
    }

    /**
     * @param array $purges
     */
    public function setPurges($purges)
    {
        $this->purges = $purges;
    }

    /**
     * @param $purge
     */
    public function addPurge($purge)
    {
        $this->purges[] = $purge;
    }

    /**
     * @return boolean
     */
    public function isPreprocess()
    {
        return $this->preprocess;
    }

    /**
     * @param boolean $preprocess
     */
    public function setPreprocess($preprocess)
    {
        $this->preprocess = (bool)$preprocess;
    }

    /**
     * @return array
     */
    public function getPreprocessMasks()
    {
        return $this->preprocessMasks;
    }

    /**
     * @param array $masks
     */
    public function setPreprocessMasks($masks)
    {
        $this->preprocessMasks = $masks;
    }

    /**
     * @param $mask
     */
    public function addPreprocessMask($mask)
    {
        $this->preprocessMasks[] = $mask;
    }

    /**
     * @return boolean
     */
    public function isPassiveMode()
    {
        return $this->passiveMode;
    }

    /**
     * @param boolean $mode
     */
    public function setPassiveMode($mode)
    {
        $this->passiveMode = (bool)$mode;
    }

    /**
     * @return string
     */
    public function getDeployFile()
    {
        return $this->deployFile;
    }

    /**
     * @param string $file
     */
    public function setDeployFile($file)
    {
        $this->deployFile = $file;
    }

    /**
     * @return boolean
     */
    public function isTestMode()
    {
        return $this->testMode;
    }

    /**
     * @param boolean $testMode
     */
    public function setTestMode($testMode)
    {
        $this->testMode = (bool)$testMode;
    }

}
