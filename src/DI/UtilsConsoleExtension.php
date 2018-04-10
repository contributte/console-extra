<?php

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Utils\UtilsRandomCommand;
use Nette\DI\CompilerExtension;

final class UtilsConsoleExtension extends CompilerExtension
{

	/**
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('random'))
			->setClass(UtilsRandomCommand::class);
	}

}
