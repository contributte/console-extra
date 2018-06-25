<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Utils;

use Contributte\Console\Extra\Exception\RuntimeException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileObject;

class Files
{

	public static function purge(string $dir): void
	{
		if (!is_dir($dir) && !mkdir($dir)) {
			throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
		}

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		/** @var SplFileObject $entry */
		foreach ($iterator as $entry) {
			if ($entry->isDir()) {
				rmdir($entry->getRealPath());
			} else {
				unlink($entry->getRealPath());
			}
		}
	}

}
