<?php declare(strict_types = 1);

namespace Tests\DI;

use Contributte\Console\Extra\Command\Database\BackupCommand;
use Contributte\Console\Extra\Command\Database\LoadCommand;
use Contributte\Console\Extra\DI\DatabaseConsoleExtension;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Nette\DI\Compiler;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(function (Compiler $compiler): void {
			$compiler->addConfig([
				'databaseBackup' => [
					'backupPath' => Environment::getTestDir() . '/backup',
					'consoleMode' => true,
				],
			]);
			$compiler->addExtension('databaseBackup', new DatabaseConsoleExtension(true));
		})->build();

	Assert::type(BackupCommand::class, $container->getService('databaseBackup.backupCommand'));
	Assert::type(LoadCommand::class, $container->getService('databaseBackup.loadCommand'));
});
