<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\DI\DIPurgeCommand;
use Nette\DI\Helpers;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class DIConsoleExtension extends AbstractCompilerExtension
{

	public static function createSchema(): Schema
	{
		return Expect::structure([
			'purge' => Expect::listOf('string'),
		]);
	}

	public function getConfigSchema(): Schema
	{
		return self::createSchema();
	}

	public function loadConfiguration(): void
	{
		// Skip if isn't CLI
		if ($this->cliMode !== true) {
			return;
		}

		$builder = $this->getContainerBuilder();
		$config = $this->config;

		// Default values cannot be in schema, arrays are merged by keys
		if ($config->purge === []) {
			$config->purge = Helpers::expand(['%tempDir%/cache/nette.configurator'], $builder->parameters);
		}

		$builder->addDefinition($this->prefix('purge'))
			->setFactory(DIPurgeCommand::class, [$config->purge]);
	}

}
