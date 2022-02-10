<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\AdvancedCache\CacheCleanCommand;
use Contributte\Console\Extra\Command\AdvancedCache\CacheGenerateCommand;
use Contributte\DI\Helper\ExtensionDefinitionsHelper;
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
		$definitionsHelper = new ExtensionDefinitionsHelper($this->compiler);

		// Register generators
		$generatorDefinitions = [];

		foreach ($config->generators as $generatorName => $generatorConfig) {
			$generatorPrefix = $this->prefix('generator.' . $generatorName);
			$generatorDefinition = $definitionsHelper->getDefinitionFromConfig($generatorConfig, $generatorPrefix);

			if ($generatorDefinition instanceof Definition) {
				$generatorDefinition->setAutowired(false);
			}

			$generatorDefinitions[$generatorName] = $generatorDefinition;
		}

		$builder->addDefinition($this->prefix('generatorCommand'))
			->setFactory(CacheGenerateCommand::class)
			->setArguments([$generatorDefinitions]);

		// Register cleaners
		$cleanerDefinitions = [];

		foreach ($config->cleaners as $cleanerName => $cleanerConfig) {
			$cleanerPrefix = $this->prefix('cleaner.' . $cleanerName);
			$cleanerDefinition = $definitionsHelper->getDefinitionFromConfig($cleanerConfig, $cleanerPrefix);

			if ($cleanerDefinition instanceof Definition) {
				$cleanerDefinition->setAutowired(false);
			}

			$cleanerDefinitions[$cleanerName] = $cleanerDefinition;
		}

		$builder->addDefinition($this->prefix('cleanCommand'))
			->setFactory(CacheCleanCommand::class)
			->setArguments([$cleanerDefinitions]);
	}

}
