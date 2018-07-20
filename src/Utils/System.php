<?php declare(strict_types = 1);

namespace Contributte\Deployer\Utils;

class System
{

	/**
	 * @param mixed $return_val
	 */
	public static function run(string $command, &$return_val): void
	{
		passthru($command, $return_val);
	}

}
