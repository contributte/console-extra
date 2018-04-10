<?php

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Security\SecurityPasswordCommand;
use Nette\DI\CompilerExtension;

final class SecurityConsoleExtension extends CompilerExtension
{

	/**
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('password'))
			->setClass(SecurityPasswordCommand::class);
	}

}
