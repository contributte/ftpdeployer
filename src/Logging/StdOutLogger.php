<?php declare(strict_types = 1);

namespace Contributte\Deployer\Logging;

use Deployment\Logger;

final class StdOutLogger extends Logger
{

	public function __construct()
	{
		/**
		 * parent constructor not called because of missing Logger interface
		 */
	}

	public function log(string $s, ?string $color = null, int $shorten = 1): void
	{
		if ($shorten === 1 && (bool) preg_match('#^\n?.*#', $s, $m)) {
			$s = $m[0];
		}

		$s .= "        \n";

		if ($this->useColors && (bool) $color) {
			$c = explode('/', (string) $color);
			$s = "\033["
				. ($c[1] === '' ? $c[1] : ';4') . sprintf('m%s\033[0m', $s);
		}

		echo $s;
	}

}
