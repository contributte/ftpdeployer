<?php declare(strict_types = 1);

namespace Contributte\Deployer\Utils;

use Nette\InvalidStateException;

class Helpers
{

	/**
	 * @param mixed[] $expected
	 * @param mixed $config
	 */
	public static function validateConfig(array $expected, $config, string $name = 'config'): void
	{
		$extra = array_diff_key((array) $config, $expected);

		if ($extra !== []) {
			$extra = implode(sprintf(', %s.', $name), array_keys($extra));
			throw new InvalidStateException(sprintf('Unknown configuration option %s.%s.', $name, $extra));
		}
	}

}
