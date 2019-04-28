<?php declare(strict_types = 1);

namespace Tests\Contributte\Console\Extra\Unit\Database\DI;

use Contributte\Console\Extra\Database\Command\BackupCommand;
use Contributte\Console\Extra\Database\Command\LoadCommand;
use Contributte\Console\Extra\Database\DI\DatabaseBackupConsoleExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../../bootstrap.php';

class DatabaseBackupConsoleExtensionTest extends TestCase
{

	public function testRegister(): void
	{
		$loader = new ContainerLoader(__DIR__ . '/../../../tmp', true);
		$class = $loader->load(function (Compiler $compiler): void {
			$compiler->addConfig([
				'databaseBackup' => [
					'backupPath' => __DIR__ . '/../../../temp/backup',
					'consoleMode' => true,
				],
			]);
			$compiler->addExtension('databaseBackup', new DatabaseBackupConsoleExtension());
		});
		/** @var Container $container */
		$container = new $class();

		Assert::type(BackupCommand::class, $container->getService('databaseBackup.backupCommand'));
		Assert::type(LoadCommand::class, $container->getService('databaseBackup.loadCommand'));
	}

}

(new DatabaseBackupConsoleExtensionTest())->run();
