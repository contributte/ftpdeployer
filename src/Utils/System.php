<?php

namespace Contributte\Deployer\Utils;

class System
{

    /**
     * @param string $command
     * @param mixed $return_val
     */
    public static function run($command, &$return_val)
    {
        passthru($command, $return_val);
    }

}
