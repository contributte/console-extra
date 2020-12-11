<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Database\BackupCommand;
use Contributte\Console\Extra\Command\Database\LoadCommand;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
class DatabaseConsoleExtension extends CompilerExtension
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
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		// Skip if it's not CLI mode
		if (!$config->consoleMode) {
			return;
		}

		$builder->addDefinition($this->prefix('backupCommand'))
			->setFactory(BackupCommand::class, [$config->backupPath])
			->setAutowired(false);

		$builder->addDefinition($this->prefix('loadCommand'))
			->setFactory(LoadCommand::class)
			->setAutowired(false);
	}

}
