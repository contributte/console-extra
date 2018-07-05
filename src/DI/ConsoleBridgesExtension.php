<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Utils\Validators;

final class ConsoleBridgesExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'advancedCache' => [],
		'cache' => [],
		'caching' => [],
		'di' => [],
		'latte' => [],
		'router' => [],
		'security' => [],
		'utils' => [],
	];

	/** @var string[] */
	private $map = [
		'advancedCache' => AdvancedCacheConsoleExtension::class,
		'cache' => CacheConsoleExtension::class,
		'caching' => CachingConsoleExtension::class,
		'di' => DIConsoleExtension::class,
		'latte' => LatteConsoleExtension::class,
		'router' => RouterConsoleExtension::class,
		'security' => SecurityConsoleExtension::class,
		'utils' => UtilsConsoleExtension::class,
	];

	/** @var CompilerExtension[] */
	private $passes = [];

	/**
	 * Register services
	 */
	public function loadConfiguration(): void
	{
		$config = $this->validateConfig($this->defaults, $this->config);

		foreach ($config as $bridge => $enabled) {
			// Don't register sub extension
			if ($enabled === false) continue;

			// Security check
			Validators::assertField($config, $bridge, 'array');

			// Register sub extension a.k.a CompilerPass
			$this->passes[$bridge] = new $this->map[$bridge]();
			$this->passes[$bridge]->setCompiler($this->compiler, $this->prefix($bridge));
			$this->passes[$bridge]->setConfig($this->config[$bridge] ?? []);
			$this->passes[$bridge]->loadConfiguration();
		}
	}

	/**
	 * Decorate services
	 */
	public function beforeCompile(): void
	{
		foreach ($this->passes as $pass) {
			$pass->beforeCompile();
		}
	}

	/**
	 * Decorate initialize method
	 */
	public function afterCompile(ClassType $class): void
	{
		foreach ($this->passes as $pass) {
			$pass->afterCompile($class);
		}
	}

}
