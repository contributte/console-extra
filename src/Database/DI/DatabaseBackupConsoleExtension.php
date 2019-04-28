<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Database\DI;

use Contributte\Console\Extra\Database\Command\BackupCommand;
use Contributte\Console\Extra\Database\Command\LoadCommand;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;

class DatabaseBackupConsoleExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'backupPath' => null,
		'consoleMode' => false,
	];

	public function loadConfiguration(): void
	{
		$config = $this->validateConfig($this->defaults);

		// Skip if it's not CLI mode
		if (!$config['consoleMode']) {
			return;
		}

		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);
		$config = Helpers::expand($config, $builder->parameters);

		$builder->addDefinition($this->prefix('backupCommand'))
			->setFactory(BackupCommand::class, [$config['backupPath']])
			->setAutowired(false);

		$builder->addDefinition($this->prefix('loadCommand'))
			->setFactory(LoadCommand::class)
			->setAutowired(false);
	}

}
