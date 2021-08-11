<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Utils\UtilsRandomCommand;

final class UtilsConsoleExtension extends AbstractCompilerExtension
{

	public function loadConfiguration(): void
	{
		// Skip if isn't CLI
		if ($this->cliMode !== true) {
			return;
		}

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('random'))
			->setFactory(UtilsRandomCommand::class);
	}

}
