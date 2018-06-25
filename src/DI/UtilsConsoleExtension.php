<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Utils\UtilsRandomCommand;
use Nette\DI\CompilerExtension;

final class UtilsConsoleExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('random'))
			->setClass(UtilsRandomCommand::class);
	}

}
