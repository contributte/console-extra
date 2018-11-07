<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Command\Security\SecurityPasswordCommand;
use Nette\DI\CompilerExtension;

final class SecurityConsoleExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('password'))
			->setFactory(SecurityPasswordCommand::class);
	}

}
