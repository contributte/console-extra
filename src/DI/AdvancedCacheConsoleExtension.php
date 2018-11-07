<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\AdvancedCache\CacheCleanCommand;
use Contributte\Console\Extra\Command\AdvancedCache\CacheGenerateCommand;
use Nette\DI\CompilerExtension;

class AdvancedCacheConsoleExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'cleaners' => [],
		'generators' => [],
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		$builder->addDefinition($this->prefix('generatorCommand'))
			->setFactory(CacheGenerateCommand::class)
			->setArguments([$config['generators']]);

		$builder->addDefinition($this->prefix('cleanCommand'))
			->setFactory(CacheCleanCommand::class)
			->setArguments([$config['cleaners']]);
	}

}
