<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Exception\LogicalException;
use Nette\DI\CompilerExtension;
use stdClass;

/**
 * @property-read stdClass $config
 * @method stdClass getConfig()
 */
abstract class AbstractCompilerExtension extends CompilerExtension
{

	public function __construct(
		protected bool $cliMode = false,
	)
	{
		if (func_num_args() <= 0) {
			throw new LogicalException(sprintf('Provide CLI mode, e.q. %s(%%consoleMode%%).', static::class));
		}
	}

}
