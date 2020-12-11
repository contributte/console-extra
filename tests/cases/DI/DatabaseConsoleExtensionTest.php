<?php declare(strict_types = 1);

namespace Tests\DI;

use Contributte\Console\Extra\Command\Database\BackupCommand;
use Contributte\Console\Extra\Command\Database\LoadCommand;
use Contributte\Console\Extra\DI\DatabaseConsoleExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class DatabaseConsoleExtensionTest extends TestCase
{

	public function testRegister(): void
	{
		$loader = new ContainerLoader(TEMP_DIR, true);
		$class = $loader->load(function (Compiler $compiler): void {
			$compiler->addConfig([
				'databaseBackup' => [
					'backupPath' => TEMP_DIR . '/backup',
					'consoleMode' => true,
				],
			]);
			$compiler->addExtension('databaseBackup', new DatabaseConsoleExtension());
		});
		/** @var Container $container */
		$container = new $class();

		Assert::type(BackupCommand::class, $container->getService('databaseBackup.backupCommand'));
		Assert::type(LoadCommand::class, $container->getService('databaseBackup.loadCommand'));
	}

}

(new DatabaseConsoleExtensionTest())->run();
