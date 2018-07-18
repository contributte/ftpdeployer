<?php

namespace Contributte\Deployer\Logging;

use Deployment\Logger;

final class StdOutLogger extends Logger
{

    /**
     */
    public function __construct()
    {
    }


    /**
     * @param string $s
     * @param string $color
     * @param bool $shorten
     */
    public function log($s, $color = NULL, $shorten = TRUE)
    {
        if ($shorten && preg_match('#^\n?.*#', $s, $m)) {
            $s = $m[0];
        }
        $s .= "        \n";
        if ($this->useColors && $color) {
            $c = explode('/', $color);
            $s = "\033["
                . (empty($c[1]) ? '' : ';4') . "m$s\033[0m";
        }
        echo $s;
    }

}
