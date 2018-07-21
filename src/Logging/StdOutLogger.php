<?php declare(strict_types = 1);

namespace Contributte\Deployer\Logging;

use Deployment\Logger;

final class StdOutLogger extends Logger
{

	public function __construct()
	{
	}


	public function log(string $s, ?string $color = null, bool $shorten = true): void
	{
		if ($shorten && preg_match('#^\n?.*#', $s, $m)) {
			$s = $m[0];
		}
		$s .= "        \n";
		if ($this->useColors && $color) {
			$c = explode('/', $color);
			$s = "\033["
				. (empty($c[1]) ? '' : ';4') . sprintf('m%s\033[0m', $s);
		}
		echo $s;
	}

}
