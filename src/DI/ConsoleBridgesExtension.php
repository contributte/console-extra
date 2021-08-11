<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class ConsoleBridgesExtension extends AbstractCompilerExtension
{

	public function getConfigSchema(): Schema
	{
		$advancedCache = AdvancedCacheConsoleExtension::createSchema();
		$cache = CacheConsoleExtension::createSchema();
		$di = DIConsoleExtension::createSchema();
		$latte = LatteConsoleExtension::createSchema();

		return Expect::structure([
			'advancedCache' => Expect::anyOf(false, $advancedCache)->default($advancedCache),
			'cache' => Expect::anyOf(false, $cache)->default($cache),
			'caching' => Expect::anyOf(false),
			'di' => Expect::anyOf(false, $di)->default($di),
			'latte' => Expect::anyOf(false, $latte)->default($latte),
			'router' => Expect::anyOf(false),
			'security' => Expect::anyOf(false),
			'utils' => Expect::anyOf(false),
		])->castTo('array');
	}

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

	public function loadConfiguration(): void
	{
		// Skip if isn't CLI
		if ($this->cliMode !== true) {
			return;
		}

		$config = $this->config;

		foreach ($config as $bridge => $bridgeConfig) {
			// Don't register sub extension

			if ($bridgeConfig === false) {
				continue;
			}

			// Register sub extension a.k.a CompilerPass
			$this->passes[$bridge] = new $this->map[$bridge]($this->cliMode);
			$this->passes[$bridge]->setCompiler($this->compiler, $this->prefix($bridge));

			if ($bridgeConfig !== null) {
				$this->passes[$bridge]->setConfig($bridgeConfig);
			}

			$this->passes[$bridge]->loadConfiguration();
		}
	}

	public function beforeCompile(): void
	{
		// Skip if isn't CLI
		if ($this->cliMode !== true) {
			return;
		}

		foreach ($this->passes as $pass) {
			$pass->beforeCompile();
		}
	}

	public function afterCompile(ClassType $class): void
	{
		// Skip if isn't CLI
		if ($this->cliMode !== true) {
			return;
		}

		foreach ($this->passes as $pass) {
			$pass->afterCompile($class);
		}
	}

}
