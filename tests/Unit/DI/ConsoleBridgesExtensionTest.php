<?php declare(strict_types = 1);

namespace Tests\Contributte\Console\Extra\Unit\DI;

use Contributte\Console\DI\ConsoleExtension;
use Contributte\Console\Extra\Command\AdvancedCache\CacheCleanCommand;
use Contributte\Console\Extra\Command\AdvancedCache\CacheGenerateCommand;
use Contributte\Console\Extra\Command\Cache\CachePurgeCommand;
use Contributte\Console\Extra\Command\Caching\CachingClearCommand;
use Contributte\Console\Extra\Command\DI\DIPurgeCommand;
use Contributte\Console\Extra\Command\Latte\LattePurgeCommand;
use Contributte\Console\Extra\Command\Latte\LatteWarmupCommand;
use Contributte\Console\Extra\Command\Router\RouterDumpCommand;
use Contributte\Console\Extra\Command\Security\SecurityPasswordCommand;
use Contributte\Console\Extra\Command\Utils\UtilsRandomCommand;
use Contributte\Console\Extra\DI\ConsoleBridgesExtension;
use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationDI\LatteExtension;
use Nette\Bridges\ApplicationDI\RoutingExtension;
use Nette\Bridges\CacheDI\CacheExtension;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\Bridges\HttpDI\SessionExtension;
use Nette\Bridges\SecurityDI\SecurityExtension;
use Nette\Caching\IStorage;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\Routing\Router;
use Nette\Schema\Processor;
use Nette\Security\Passwords;
use Symfony\Component\Console\Command\Command;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class ConsoleBridgesExtensionTest extends TestCase
{

	/** @var ConsoleBridgesExtension */
	private $extension;

	/**
	 * Creates Nette DI container
	 *
	 * @param mixed[] $config Configuration
	 * @return Container Nette DI container
	 */
	private function createContainer(array $config = []): Container
	{
		$loader = new ContainerLoader(TEMP_DIR, true);
		$configuration = array_merge_recursive([
			'parameters' => [
				'appDir' => TEMP_DIR . '/app',
				'tempDir' => TEMP_DIR . '/tmp',
			],
		], $config);
		$class = $loader->load(function (Compiler $compiler) use ($configuration): void {
			$compiler->addExtension('cache', new CacheExtension(TEMP_DIR . '/tmp'));
			$compiler->addExtension('console', new ConsoleExtension(true));
			$compiler->addExtension('console.extra', $this->extension);
			$compiler->addExtension('http', new HttpExtension(true));
			$compiler->addExtension('latte', new LatteExtension(TEMP_DIR . '/tmp'));
			$compiler->addExtension('router', new RoutingExtension());
			$compiler->addExtension('security', new SecurityExtension());
			$compiler->addExtension('session', new SessionExtension(false, true));
			$compiler->addConfig($configuration);
		});

		return new $class();
	}

	/**
	 * Tests the method to
	 */
	public function testGetConfigSchemaComponents(): void
	{
		$schema = $this->extension->getConfigSchema();
		$processor = new Processor();
		$config = $processor->process($schema, []);
		$components = ['advancedCache', 'cache', 'caching', 'di', 'latte', 'router', 'security', 'utils'];

		foreach (array_keys($config) as $name) {
			Assert::true(in_array($name, $components));
		}
	}

	/**
	 * Tests DI extension without the configuration
	 */
	public function testDiWithoutConfiguration(): void
	{
		$container = $this->createContainer([]);
		/** @var Container $container Nette DI container */
		$cacheDir = TEMP_DIR . '/tmp/cache';
		$passwords = new Passwords();
		$router = $container->getByType(Router::class);
		$storage = $container->getByType(IStorage::class);
		$templateFactory = $container->getByType(ITemplateFactory::class);
		$commands = [
			CacheCleanCommand::class => new CacheCleanCommand([]),
			CacheGenerateCommand::class => new CacheGenerateCommand([]),
			CachePurgeCommand::class => new CachePurgeCommand([$cacheDir]),
			CachingClearCommand::class => new CachingClearCommand($storage),
			DIPurgeCommand::class => new DIPurgeCommand([$cacheDir . '/nette.configurator']),
			LattePurgeCommand::class => new LattePurgeCommand([$cacheDir . '/latte']),
			LatteWarmupCommand::class => new LatteWarmupCommand($templateFactory, [TEMP_DIR . '/app']),
			RouterDumpCommand::class => new RouterDumpCommand($router),
			SecurityPasswordCommand::class => new SecurityPasswordCommand($passwords),
			UtilsRandomCommand::class => new UtilsRandomCommand(),
		];

		foreach ($commands as $class => $expected) {
			Assert::equal($expected, $container->getByType($class));
		}

		Assert::equal(10, count($container->findByType(Command::class)));
	}

	/**
	 * Sets up the test environment
	 */
	protected function setUp(): void
	{
		$this->extension = new ConsoleBridgesExtension();
	}

}

(new ConsoleBridgesExtensionTest())->run();
