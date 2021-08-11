<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Caching\CachingClearCommand;

final class CachingConsoleExtension extends AbstractCompilerExtension
{

	public function loadConfiguration(): void
	{
		// Skip if isn't CLI
		if ($this->cliMode !== true) {
			return;
		}

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('clear'))
			->setFactory(CachingClearCommand::class);
	}

}
