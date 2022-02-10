<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Utils;

use Contributte\Console\Extra\Exception\RuntimeException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileObject;

class Files
{

	/**
	 * @param string[] $ignored
	 */
	public static function purge(string $dir, array $ignored = []): void
	{
		if (!is_dir($dir) && !mkdir($dir)) {
			throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
		}

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		$ignored = array_map(function (string $path) {
			return realpath($path);
		}, $ignored);

		/** @var SplFileObject $entry */
		foreach ($iterator as $entry) {
			if (!in_array(str_replace('\\', '/', (string) $entry->getRealPath()), $ignored, true)) {
				if ($entry->isDir()) {
					rmdir((string) $entry->getRealPath());
				} else {
					unlink((string) $entry->getRealPath());
				}
			}
		}
	}

}
