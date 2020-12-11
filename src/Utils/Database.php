<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Utils;

class Database
{

	public const PLATFORM_POSTGRES = 'postgresql';
	public const PLATFORM_MYSQL = 'mysql';

	/**
	 * @param string[] $libs
	 */
	public static function normalizeBinPath(?string $binPath, array $libs): string
	{
		return $binPath !== null ? rtrim(str_replace($libs, '', $binPath), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : '';
	}

	public static function isSql(string $fileName): bool
	{
		return self::endsWith(strtolower($fileName), '.sql');
	}

	public static function isGz(string $fileName): bool
	{
		return self::endsWith(strtolower($fileName), '.gz');
	}

	private static function endsWith(string $haystack, string $needle): bool
	{
		return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
	}

}
