<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Latte\LattePurgeCommand;
use Contributte\Console\Extra\Command\Latte\LatteWarmupCommand;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
final class LatteConsoleExtension extends CompilerExtension
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
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		// Default values cannot be in schema, arrays are merged by keys
		if (!isset($config->warmup) || ($config->warmup === [])) {
			$config->warmup = Helpers::expand(['%appDir%'], $builder->parameters);
		}

		if (!isset($config->purge) || ($config->purge === [])) {
			$config->purge = Helpers::expand(['%tempDir%/cache/latte'], $builder->parameters);
		}

		$builder->addDefinition($this->prefix('warmup'))
			->setFactory(LatteWarmupCommand::class, [
				1 => $config->warmup,
				2 => $config->warmupExclude ?? [],
			]);

		$builder->addDefinition($this->prefix('purge'))
			->setFactory(LattePurgeCommand::class, [$config->purge]);
	}

}
