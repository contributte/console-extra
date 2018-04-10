<?php

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Latte\LattePurgeCommand;
use Contributte\Console\Extra\Command\Latte\LatteWarmupCommand;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;

final class LatteConsoleExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'warmup' => [
			'%appDir%',
		],
		'purge' => [
			'%tempDir%/latte',
		],
	];

	/**
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		// Don't use predefined default values, if user provide it
		if (isset($this->config['warmup'])) $this->defaults['warmup'] = [];
		if (isset($this->config['purge'])) $this->defaults['purge'] = [];

		$config = $this->validateConfig($this->defaults);
		$config = Helpers::expand($config, $builder->parameters);

		$builder->addDefinition($this->prefix('warmup'))
			->setClass(LatteWarmupCommand::class, [$config['warmup']]);

		$builder->addDefinition($this->prefix('purge'))
			->setClass(LattePurgeCommand::class, [$config['purge']]);
	}

}
