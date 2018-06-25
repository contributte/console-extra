<?php declare(strict_types = 1);

/**
 * Test: Utils\Files
 */

use Contributte\Console\Extra\Utils\Files;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
	file_put_contents(TEMP_DIR . '/foo.txt', 'foo');
	file_put_contents(TEMP_DIR . '/bar.txt', 'bar');

	Assert::equal(['.', '..', 'bar.txt', 'foo.txt'], scandir(TEMP_DIR));

	Files::purge(TEMP_DIR);
	Assert::equal(['.', '..'], scandir(TEMP_DIR));
});
