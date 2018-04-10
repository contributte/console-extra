<?php

namespace Contributte\Console\Extra\DI;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Utils\Validators;

final class ConsoleBridgesExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
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
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$config = $this->validateConfig($this->defaults, $this->config);

		foreach ($config as $bridge => $enabled) {
			// Don't register sub extension
			if ($enabled === FALSE) continue;

			// Security check
			Validators::assertField($config, $bridge, 'array');

			// Register sub extension a.k.a CompilerPass
			$this->passes[$bridge] = new $this->map[$bridge]();
			$this->passes[$bridge]->setCompiler($this->compiler, $this->prefix($bridge));
			$this->passes[$bridge]->setConfig(isset($this->config[$bridge]) ? $this->config[$bridge] : []);
			$this->passes[$bridge]->loadConfiguration();
		}
	}

	/**
	 * Decorate services
	 *
	 * @return void
	 */
	public function beforeCompile()
	{
		foreach ($this->passes as $pass) {
			$pass->beforeCompile();
		}
	}

	/**
	 * Decorate initialize method
	 *
	 * @param ClassType $class
	 * @return void
	 */
	public function afterCompile(ClassType $class)
	{
		foreach ($this->passes as $pass) {
			$pass->afterCompile($class);
		}
	}

}
