<?php declare(strict_types = 1);

namespace Tests\Cases\Utils;

use Contributte\Console\Extra\Utils\Files;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	file_put_contents(Environment::getTestDir() . '/foo.txt', 'foo');
	file_put_contents(Environment::getTestDir() . '/bar.txt', 'bar');

	Assert::equal(['.', '..', 'bar.txt', 'foo.txt'], scandir(Environment::getTestDir()));

	Files::purge(Environment::getTestDir());
	Assert::equal(['.', '..'], scandir(Environment::getTestDir()));
});
