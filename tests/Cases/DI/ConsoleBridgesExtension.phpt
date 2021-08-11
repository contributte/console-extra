<?php declare(strict_types = 1);

namespace Tests\DI;

use Contributte\Console\Application;
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
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Nette\Caching\Storage;
use Nette\DI\MissingServiceException;
use Nette\Routing\Router;
use Nette\Schema\Processor;
use Nette\Security\Passwords;
use Ninjify\Nunjuck\Toolkit;
use Symfony\Component\Console\Command\Command;
use Tester\Assert;
use Tests\Toolkit\Container;
use Tests\Toolkit\Tests;

require_once __DIR__ . '/../../bootstrap.php';

// Get schema
Toolkit::test(function (): void {
	$schema = (new ConsoleBridgesExtension(true))->getConfigSchema();
	$processor = new Processor();
	$config = $processor->process($schema, []);
	$components = ['advancedCache', 'cache', 'caching', 'di', 'latte', 'router', 'security', 'utils'];

	foreach (array_keys($config) as $name) {
		Assert::true(in_array($name, $components));
	}
});

// Default config
Toolkit::test(function (): void {
	$container = Container::of()
		->withDefaults()
		->build();

	$passwords = new Passwords();
	$router = $container->getByType(Router::class);
	$storage = $container->getByType(Storage::class);
	$templateFactory = $container->getByType(TemplateFactory::class);

	Assert::equal(10, count($container->findByType(Command::class)));

	$commands = [
		CacheCleanCommand::class => new CacheCleanCommand([]),
		CacheGenerateCommand::class => new CacheGenerateCommand([]),
		CachePurgeCommand::class => new CachePurgeCommand([Tests::TEMP_PATH . '/cache']),
		CachingClearCommand::class => new CachingClearCommand($storage),
		DIPurgeCommand::class => new DIPurgeCommand([Tests::TEMP_PATH . '/cache/nette.configurator']),
		LattePurgeCommand::class => new LattePurgeCommand([Tests::TEMP_PATH . '/cache/latte']),
		LatteWarmupCommand::class => new LatteWarmupCommand($templateFactory, [Tests::APP_PATH]),
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
	$container = Container::of()
		->withDefaults()
		->withConsoleMode(false)
		->build();

	Assert::exception(static function () use ($container): void {
		$container->getByType(Application::class);
	}, MissingServiceException::class);
});
