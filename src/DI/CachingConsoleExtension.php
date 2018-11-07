<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Caching\CachingClearCommand;
use Nette\DI\CompilerExtension;

final class CachingConsoleExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('clear'))
			->setFactory(CachingClearCommand::class);
	}

}
