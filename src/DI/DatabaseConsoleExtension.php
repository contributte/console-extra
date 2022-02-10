<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Database\BackupCommand;
use Contributte\Console\Extra\Command\Database\LoadCommand;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class DatabaseConsoleExtension extends AbstractCompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'backupPath' => Expect::string()->required(),
			'consoleMode' => Expect::bool(false),
		]);
	}

	public function loadConfiguration(): void
	{
		// Skip if isn't CLI
		if ($this->cliMode !== true) {
			return;
		}

		$builder = $this->getContainerBuilder();
		$config = $this->config;

		$builder->addDefinition($this->prefix('backupCommand'))
			->setFactory(BackupCommand::class, [$config->backupPath])
			->setAutowired(false);

		$builder->addDefinition($this->prefix('loadCommand'))
			->setFactory(LoadCommand::class)
			->setAutowired(false);
	}

}
