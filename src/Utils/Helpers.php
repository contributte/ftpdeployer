<?php

namespace Minetro\Deployer\Utils;

class Helpers
{

    /**
     * @param $val
     * @param bool $lines
     * @return array
     */
    public static function toArray($val, $lines = FALSE)
    {
        return is_array($val)
            ? array_filter($val)
            : preg_split($lines ? '#\s*\n\s*#' : '#\s+#', $val, -1, PREG_SPLIT_NO_EMPTY);
    }

}
