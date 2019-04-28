<?php declare(strict_types = 1);

namespace Tests\Contributte\Console\Extra\Unit\Utils;

use Contributte\Console\Extra\Utils\Files;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class FilesTest extends TestCase
{

	public function testOk(): void
	{
		file_put_contents(TEMP_DIR . '/foo.txt', 'foo');
		file_put_contents(TEMP_DIR . '/bar.txt', 'bar');

		Assert::equal(['.', '..', 'bar.txt', 'foo.txt'], scandir(TEMP_DIR));

		Files::purge(TEMP_DIR);
		Assert::equal(['.', '..'], scandir(TEMP_DIR));
	}

}

(new FilesTest())->run();
