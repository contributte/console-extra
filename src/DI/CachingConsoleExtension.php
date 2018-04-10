<?php

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Caching\CachingClearCommand;
use Nette\DI\CompilerExtension;

final class CachingConsoleExtension extends CompilerExtension
{

	/**
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('clear'))
			->setClass(CachingClearCommand::class);
	}

}
