<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Utils;

class Utils
{

	public static function stringify(mixed $input): string
	{
		if (is_array($input)) {
			return implode('|', $input);
		}

		return strval($input);
	}

}
