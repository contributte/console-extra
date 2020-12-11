<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Utils;

class Utils
{

	/**
	 * @param mixed $input
	 */
	public static function stringify($input): string
	{
		if (is_array($input)) {
			return implode('|', $input);
		}

		return strval($input);
	}

}
