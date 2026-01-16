<?php declare(strict_types = 1);

namespace Tests\Cases\Utils;

use Contributte\Console\Extra\Command\Latte\LatteWarmupCommand;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Latte\Engine;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

Toolkit::test(function (): void {
	$templateFactory = new TemplateFactory(
		new class implements LatteFactory {

			public function create(): Engine
			{
				$latte = new Engine();
				$latte->setTempDirectory(Environment::getTestDir());

				return $latte;
			}

		}
	);

	$application = new Application();
	$application->addCommand(new LatteWarmupCommand(
		$templateFactory,
		[__DIR__ . '/../../../Fixtures/Latte']
	));

	$command = $application->find('nette:latte:warmup');
	$commandTester = new CommandTester($command);
	$commandTester->execute([]);
	Assert::equal(<<<'OUTPUT'
Latte Warmup
============

 [WARNING] Warmup partial done. 0 success / 1 errors. Total 1 files.
OUTPUT, trim($commandTester->getDisplay()));
	Assert::match('.|..|Fixtures-Latte-template.latte--%a%.php.lock', implode('|', scandir(Environment::getTestDir())));
});
