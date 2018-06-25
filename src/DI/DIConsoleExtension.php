<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\DI\DIPurgeCommand;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;

final class DIConsoleExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'purge' => [
			'%tempDir%/cache/Nette.Configurator',
		],
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		// Don't use predefined default values, if user provide it
		if (isset($this->config['purge'])) $this->defaults['purge'] = [];

		$config = $this->validateConfig($this->defaults);
		$config = Helpers::expand($config, $builder->parameters);

		$builder->addDefinition($this->prefix('purge'))
			->setClass(DIPurgeCommand::class, [$config['purge']]);
	}

}
