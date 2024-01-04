<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Utils;

use Contributte\Console\Extra\Exception\LogicalException;

class Utils
{

	public static function stringify(mixed $input): string
	{
		if (is_array($input)) {
			return implode('|', $input);
		}

		if (is_scalar($input)) {
			return (string) $input;
		}

		throw new LogicalException(sprintf('Cannot stringify %s', gettype($input)));
	}

	public static function numerize(mixed $input): int
	{
		if (is_scalar($input)) {
			return (int) $input;
		}

		throw new LogicalException(sprintf('Cannot numerize %s', gettype($input)));
	}

	public static function boolenize(mixed $input): bool
	{
		if (is_scalar($input)) {
			return filter_var($input, FILTER_VALIDATE_BOOLEAN);
		}

		throw new LogicalException(sprintf('Cannot boolenize %s', gettype($input)));
	}

}
