<?php declare(strict_types = 1);

namespace Tests\Toolkit;

use Contributte\Console\DI\ConsoleExtension;
use Contributte\Console\Extra\DI\ConsoleBridgesExtension;
use Nette\Bridges\ApplicationDI\LatteExtension;
use Nette\Bridges\ApplicationDI\RoutingExtension;
use Nette\Bridges\CacheDI\CacheExtension;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\Bridges\HttpDI\SessionExtension;
use Nette\Bridges\SecurityDI\SecurityExtension;
use Nette\DI\Compiler;
use Nette\DI\Container as NetteContainer;
use Nette\DI\ContainerLoader;

final class Container
{

	/** @var string */
	private $key;

	/** @var callable[] */
	private $onCompile = [];

	/** @var bool */
	private $consoleMode = true;

	public function __construct(string $key)
	{
		$this->key = $key;
	}

	public static function of(?string $key = null): Container
	{
		return new static($key ?? uniqid(random_bytes(16)));
	}

	public function withDefaults(): Container
	{
		$this->withDefaultExtensions();
		$this->withDefaultParameters();

		return $this;
	}

	public function withDefaultExtensions(): Container
	{
		$this->onCompile[] = function (Compiler $compiler): void {
			$compiler->addExtension('cache', new CacheExtension(TEMP_DIR . '/cache'));
			$compiler->addExtension('console', new ConsoleExtension($this->consoleMode));
			$compiler->addExtension('console.extra', new ConsoleBridgesExtension($this->consoleMode));
			$compiler->addExtension('http', new HttpExtension($this->consoleMode));
			$compiler->addExtension('latte', new LatteExtension(TEMP_DIR . '/latte'));
			$compiler->addExtension('router', new RoutingExtension());
			$compiler->addExtension('security', new SecurityExtension());
			$compiler->addExtension('session', new SessionExtension(false, $this->consoleMode));
		};

		return $this;
	}

	public function withDefaultParameters(): Container
	{
		$this->onCompile[] = function (Compiler $compiler): void {
			$compiler->addConfig([
				'parameters' => [
					'tempDir' => Tests::TEMP_PATH,
					'appDir' => Tests::APP_PATH,
				],
			]);
		};

		return $this;
	}

	public function withCompiler(callable $cb): Container
	{
		$this->onCompile[] = function (Compiler $compiler) use ($cb): void {
			$cb($compiler);
		};

		return $this;
	}

	public function withConsoleMode(bool $mode): Container
	{
		$this->consoleMode = $mode;

		return $this;
	}

	public function build(): NetteContainer
	{
		$loader = new ContainerLoader(Tests::TEMP_PATH, true);
		$class = $loader->load(function (Compiler $compiler): void {
			foreach ($this->onCompile as $cb) {
				$cb($compiler);
			}
		}, $this->key);

		return new $class();
	}

}
