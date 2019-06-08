<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\DI;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * @property-read mixed[] $config
 */
final class ConsoleBridgesExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'advancedCache' => Expect::anyOf(false, AdvancedCacheConsoleExtension::createSchema())->default([]),
			'cache' => Expect::anyOf(false, CacheConsoleExtension::createSchema())->default([]),
			'caching' => Expect::anyOf(false)->default([]),
			'di' => Expect::anyOf(false, DIConsoleExtension::createSchema())->default([]),
			'latte' => Expect::anyOf(false, LatteConsoleExtension::createSchema())->default([]),
			'router' => Expect::anyOf(false)->default([]),
			'security' => Expect::anyOf(false)->default([]),
			'utils' => Expect::anyOf(false)->default([]),
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
		$config = $this->config;

		foreach ($config as $bridge => $bridgeConfig) {
			// Don't register sub extension
			if ($bridgeConfig === false) {
				continue;
			}

			// Register sub extension a.k.a CompilerPass
			$this->passes[$bridge] = new $this->map[$bridge]();
			$this->passes[$bridge]->setCompiler($this->compiler, $this->prefix($bridge));
			$this->passes[$bridge]->setConfig($bridgeConfig);
			$this->passes[$bridge]->loadConfiguration();
		}
	}

	public function beforeCompile(): void
	{
		foreach ($this->passes as $pass) {
			$pass->beforeCompile();
		}
	}

	public function afterCompile(ClassType $class): void
	{
		foreach ($this->passes as $pass) {
			$pass->afterCompile($class);
		}
	}

}
