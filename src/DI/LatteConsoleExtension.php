<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Latte\LattePurgeCommand;
use Contributte\Console\Extra\Command\Latte\LatteWarmupCommand;
use Nette\DI\Helpers;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class LatteConsoleExtension extends AbstractCompilerExtension
{

	public static function createSchema(): Schema
	{
		return Expect::structure([
			'warmup' => Expect::listOf('string'),
			'warmupExclude' => Expect::listOf('string'),
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
		if ($config->warmup === []) {
			$config->warmup = Helpers::expand(['%appDir%'], $builder->parameters);
		}

		if ($config->purge === []) {
			$config->purge = Helpers::expand(['%tempDir%/cache/latte'], $builder->parameters);
		}

		$builder->addDefinition($this->prefix('warmup'))
			->setFactory(LatteWarmupCommand::class, [
				1 => $config->warmup,
				2 => $config->warmupExclude,
			]);

		$builder->addDefinition($this->prefix('purge'))
			->setFactory(LattePurgeCommand::class, [$config->purge]);
	}

}
