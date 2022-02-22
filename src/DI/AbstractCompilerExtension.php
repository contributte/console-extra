<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Contributte\Console\Extra\Exception\Logical\InvalidArgumentException;
use Nette\DI\CompilerExtension;
use stdClass;

/**
 * @property-read stdClass $config
 */
abstract class AbstractCompilerExtension extends CompilerExtension
{

	/** @var bool */
	protected $cliMode;

	public function __construct(bool $cliMode = false)
	{
		if (func_num_args() <= 0) {
			throw new InvalidArgumentException(sprintf('Provide CLI mode, e.q. %s(%%consoleMode%%).', static::class));
		}

		$this->cliMode = $cliMode;
	}

}
