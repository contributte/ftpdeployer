<?php

namespace Minetro\Deployer\Config;

class Config
{

    /** Modes */
    const MODE_GENERATE = 'generate';
    const MODE_TEST = 'test';
    const MODE_RUN = 'deploy';

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

    /** @var array */
    private $userdata = [];

    /** @var array */
    private $plugins = [];

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * @param string $logFile
     */
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;
    }

    /**
     * @return boolean
     */
    public function useColors()
    {
        return $this->colors;
    }

    /**
     * @param boolean $colors
     */
    public function setColors($colors)
    {
        $this->colors = (bool)$colors;
    }

    /**
     * @return string
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }

    /**
     * @param string $tempDir
     */
    public function setTempDir($tempDir)
    {
        $this->tempDir = $tempDir;
    }

    /**
     * @return Section[]
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param Section $sections
     */
    public function setSections($sections)
    {
        $this->sections = $sections;
    }

    /**
     * @param Section $section
     */
    public function addSection(Section $section)
    {
        $this->sections[] = $section;
    }

    /**
     * @return array
     */
    public function getUserdata()
    {
        return $this->userdata;
    }

    /**
     * @param array $userdata
     */
    public function setUserdata($userdata)
    {
        $this->userdata = $userdata;
    }

    /**
     * @param string $key
     * @param mixed $data
     */
    public function addUserdata($key, $data)
    {
        $this->userdata[$key] = $data;
    }

    /**
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @param array $plugins
     */
    public function setPlugins($plugins)
    {
        $this->plugins = $plugins;
    }

    /**
     * @param string $name
     * @param array $data
     */
    public function addPlugin($name, array $data)
    {
        $this->plugins[$name] = $data;
    }

}
