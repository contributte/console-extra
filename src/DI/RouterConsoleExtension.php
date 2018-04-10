<?php

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Router\RouterDumpCommand;
use Nette\DI\CompilerExtension;

final class RouterConsoleExtension extends CompilerExtension
{

	/**
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('dump'))
			->setClass(RouterDumpCommand::class);
	}

}
