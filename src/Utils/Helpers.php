<?php

namespace Minetro\Deployer\Utils;

use Nette\InvalidStateException;

class Helpers
{

    /**
     * @param string $command
     * @param mixed $return_val
     */
    public static function validateConfig($expected, $config, $name = 'config')
    {
        if ($extra = array_diff_key((array)$config, $expected)) {
            $extra = implode(", $name.", array_keys($extra));
            throw new InvalidStateException("Unknown configuration option $name.$extra.");
        }
    }

}
