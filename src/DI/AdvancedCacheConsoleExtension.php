<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\AdvancedCache\CacheCleanCommand;
use Contributte\Console\Extra\Command\AdvancedCache\CacheGenerateCommand;
use Nette\DI\Definitions\Definition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class AdvancedCacheConsoleExtension extends AbstractCompilerExtension
{

	public static function createSchema(): Schema
	{
		return Expect::structure([
			'cleaners' => Expect::arrayOf(
				Expect::anyOf(Expect::string(), Expect::array(), Expect::type(Statement::class))
			),
			'generators' => Expect::arrayOf(
				Expect::anyOf(Expect::string(), Expect::array(), Expect::type(Statement::class))
			),
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

		// Register generators
		$generatorDefinitions = [];

		foreach ($config->generators as $generatorName => $generatorConfig) {
			$generatorDef = $builder->addDefinition($this->prefix('generator.' . $generatorName))
				->setFactory($generatorConfig)
				->setAutowired(false);

			$generatorDefinitions[$generatorName] = $generatorDef;
		}

		$builder->addDefinition($this->prefix('generatorCommand'))
			->setFactory(CacheGenerateCommand::class)
			->setArguments([$generatorDefinitions]);

		// Register cleaners
		$cleanerDefinitions = [];

		foreach ($config->cleaners as $cleanerName => $cleanerConfig) {
			$cleanerDef = $builder->addDefinition($this->prefix('cleaner.' . $cleanerName))
				->setFactory($cleanerConfig)
				->setAutowired(false);

			$cleanerDefinitions[$cleanerName] = $cleanerDef;
		}

		$builder->addDefinition($this->prefix('cleanCommand'))
			->setFactory(CacheCleanCommand::class)
			->setArguments([$cleanerDefinitions]);
	}

}
