<?php declare(strict_types = 1);

namespace Tests\DI;

use Contributte\Console\Application;
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
use Contributte\Console\Extra\DI\DatabaseConsoleExtension;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\ContainerBuilder;
use Nette\Bridges\ApplicationDI\LatteExtension;
use Nette\Bridges\ApplicationDI\RoutingExtension;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Nette\Bridges\CacheDI\CacheExtension;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\Bridges\HttpDI\SessionExtension;
use Nette\Bridges\SecurityDI\SecurityExtension;
use Nette\Caching\Storage;
use Nette\DI\Compiler;
use Nette\DI\MissingServiceException;
use Nette\Routing\Router;
use Nette\Schema\Processor;
use Nette\Security\Passwords;
use Symfony\Component\Console\Command\Command;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

const APP_PATH = __DIR__ . '/..';
const TEMP_PATH = __DIR__ . '/../tmp';

// Get schema
Toolkit::test(function (): void {
	$schema = (new ConsoleBridgesExtension(true))->getConfigSchema();
	$processor = new Processor();
	$config = $processor->process($schema, []);
	$components = ['advancedCache', 'cache', 'caching', 'di', 'latte', 'router', 'security', 'utils'];

	foreach (array_keys($config) as $name) {
		Assert::contains($name, $components);
	}
});

// Default config
Toolkit::test(function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(function (Compiler $compiler): void {
			$compiler->addExtension('cache', new CacheExtension(Environment::getTestDir() . '/cache'));
			$compiler->addExtension('console', new ConsoleExtension(true));
			$compiler->addExtension('console.extra', new ConsoleBridgesExtension(true));
			$compiler->addExtension('http', new HttpExtension(true));
			$compiler->addExtension('latte', new LatteExtension(Environment::getTestDir() . '/latte'));
			$compiler->addExtension('router', new RoutingExtension());
			$compiler->addExtension('security', new SecurityExtension());
			$compiler->addExtension('session', new SessionExtension(false, true));
			$compiler->addConfig([
				'databaseBackup' => [
					'backupPath' => Environment::getTestDir() . '/backup',
					'consoleMode' => true,
				],
			]);
			$compiler->addConfig([
				'parameters' => [
					'tempDir' => TEMP_PATH,
					'appDir' => APP_PATH,
				],
			]);
			$compiler->addExtension('databaseBackup', new DatabaseConsoleExtension(true));
		})->build();

	$passwords = new Passwords();
	$router = $container->getByType(Router::class);
	$storage = $container->getByType(Storage::class);
	$templateFactory = $container->getByType(TemplateFactory::class);

	Assert::count(12, $container->findByType(Command::class));

	$commands = [
		CacheCleanCommand::class => new CacheCleanCommand([]),
		CacheGenerateCommand::class => new CacheGenerateCommand([]),
		CachePurgeCommand::class => new CachePurgeCommand([TEMP_PATH . '/cache']),
		CachingClearCommand::class => new CachingClearCommand($storage),
		DIPurgeCommand::class => new DIPurgeCommand([TEMP_PATH . '/cache/nette.configurator']),
		LattePurgeCommand::class => new LattePurgeCommand([TEMP_PATH . '/cache/latte']),
		LatteWarmupCommand::class => new LatteWarmupCommand($templateFactory, [APP_PATH]),
		RouterDumpCommand::class => new RouterDumpCommand($router),
		SecurityPasswordCommand::class => new SecurityPasswordCommand($passwords),
		UtilsRandomCommand::class => new UtilsRandomCommand(),
	];

	foreach ($commands as $class => $expected) {
		Assert::equal($expected, $container->getByType($class));
	}
});

// Non-CLI mode
Toolkit::test(function (): void {
	$container = ContainerBuilder::of()
		->withCompiler(function (Compiler $compiler): void {
			$compiler->addExtension('cache', new CacheExtension(Environment::getTestDir() . '/cache'));
			$compiler->addExtension('console', new ConsoleExtension(false));
			$compiler->addExtension('console.extra', new ConsoleBridgesExtension(false));
			$compiler->addExtension('http', new HttpExtension(false));
			$compiler->addExtension('latte', new LatteExtension(Environment::getTestDir() . '/latte'));
			$compiler->addExtension('router', new RoutingExtension());
			$compiler->addExtension('security', new SecurityExtension());
			$compiler->addExtension('session', new SessionExtension(false, false));
			$compiler->addConfig([
				'databaseBackup' => [
					'backupPath' => Environment::getTestDir() . '/backup',
					'consoleMode' => true,
				],
			]);
			$compiler->addConfig([
				'parameters' => [
					'tempDir' => TEMP_PATH,
					'appDir' => APP_PATH,
				],
			]);
			$compiler->addExtension('databaseBackup', new DatabaseConsoleExtension(true));
		})->build();

	Assert::exception(static function () use ($container): void {
		$container->getByType(Application::class);
	}, MissingServiceException::class);
});
