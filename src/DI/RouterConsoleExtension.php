<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Router\RouterDumpCommand;
use Nette\DI\CompilerExtension;

final class RouterConsoleExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('dump'))
			->setFactory(RouterDumpCommand::class);
	}

}
