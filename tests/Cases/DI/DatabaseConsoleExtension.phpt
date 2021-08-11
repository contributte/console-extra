<?php declare(strict_types = 1);

namespace Tests\DI;

use Contributte\Console\Extra\Command\Database\BackupCommand;
use Contributte\Console\Extra\Command\Database\LoadCommand;
use Contributte\Console\Extra\DI\DatabaseConsoleExtension;
use Nette\DI\Compiler;
use Ninjify\Nunjuck\Toolkit;
use Tester\Assert;
use Tests\Toolkit\Container;

require_once __DIR__ . '/../../bootstrap.php';

Toolkit::test(function (): void {
	$container = Container::of()
		->withCompiler(function (Compiler $compiler): void {
			$compiler->addConfig([
				'databaseBackup' => [
					'backupPath' => TEMP_DIR . '/backup',
					'consoleMode' => true,
				],
			]);
			$compiler->addExtension('databaseBackup', new DatabaseConsoleExtension(true));
		})->build();

	Assert::type(BackupCommand::class, $container->getService('databaseBackup.backupCommand'));
	Assert::type(LoadCommand::class, $container->getService('databaseBackup.loadCommand'));
});
