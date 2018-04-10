<?php

namespace Contributte\Console\Extra\Utils;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Files
{

	/**
	 * @param string $dir
	 * @return void
	 */
	public static function purge($dir)
	{
		if (!is_dir($dir)) {
			mkdir($dir);
		}
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($iterator as $entry) {
			if ($entry->isDir()) {
				rmdir($entry);
			} else {
				unlink($entry);
			}
		}
	}

}
