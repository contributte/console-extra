<?php

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Cache\CachePurgeCommand;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;

final class CacheConsoleExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'purge' => [
			'%tempDir%/cache',
		],
	];

	/**
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		// Don't use predefined default values, if user provide it
		if (isset($this->config['purge'])) $this->defaults['purge'] = [];

		$config = $this->validateConfig($this->defaults);
		$config = Helpers::expand($config, $builder->parameters);

		$builder->addDefinition($this->prefix('purge'))
			->setClass(CachePurgeCommand::class, [$config['purge']]);
	}

}
