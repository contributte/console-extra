<?php declare(strict_types = 1);

namespace Tests\Cases\Command\Router;

use Contributte\Console\Extra\Command\Router\RouterDumpCommand;
use Contributte\Tester\Toolkit;
use Nette\Application\Routers\RouteList;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

Toolkit::test(function (): void {
	$router = new RouteList();
	$router->addRoute('api/<presenter>/<action>', [
		'presenter' => 'Api:Dashboard',
		'action' => 'default',
	]);

	$admin = $router->withModule('Admin');
	$admin->addRoute('admin/<presenter>/<action>[/<id>]', [
		'presenter' => 'Dashboard',
		'action' => 'default',
		'id' => 1,
		'lang' => 'cs',
		'secured' => true,
		'meta' => ['role' => 'admin'],
	]);

	$application = new Application();
	$application->addCommand(new RouterDumpCommand($router));

	$command = $application->find('nette:router:dump');
	$commandTester = new CommandTester($command);
	$commandTester->execute([]);

	$display = $commandTester->getDisplay();
	$analyseDefaults = \Closure::bind(
		fn (array $defaults): string => $this->analyseDefaults($defaults),
		$command,
		RouterDumpCommand::class,
	);

	Assert::true(str_contains($display, 'api/<presenter>/<action>'));
	Assert::true(str_contains($display, 'Api:Dashboard:default'));
	Assert::true(str_contains($display, 'admin/<presenter>/<action>[/<id>]'));
	Assert::true(str_contains($display, 'Admin'));
	Assert::true(str_contains($display, 'Dashboard:default:1 [lang=>cs,secured=>1]'));
	Assert::same('Dashboard:default:1 [lang=>cs,secured=>true,meta=>array]', $analyseDefaults([
		'presenter' => 'Dashboard',
		'action' => 'default',
		'id' => 1,
		'lang' => 'cs',
		'secured' => true,
		'meta' => ['role' => 'admin'],
	]));
});
