<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Router;

use Contributte\Console\Extra\Exception\LogicalException;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;
use stdClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
	name: 'nette:router:dump',
	description: 'Display all defined routes',
)]
final class RouterDumpCommand extends Command
{

	public function __construct(
		private readonly Router $router,
	)
	{
		parent::__construct();
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$table = new Table($output);
		$table
			->setHeaders(['Mask', 'Module', 'Defaults', 'Router'])
			->setRows($this->createRows());

		$table->render();

		return 0;
	}

	/**
	 * @return array<mixed>
	 */
	protected function createRows(): array
	{
		return $this->analyse($this->router);
	}

	/**
	 * @return array<mixed>
	 */
	protected function analyse(Router $router, ?string $module = null): array
	{
		if ($router instanceof RouteList) {
			$routes = $this->analyseRouteList($router, $module);
		} elseif ($router instanceof Route) {
			$routes = [(array) $this->analyseRoute($router, $module)];
		} else {
			throw new LogicalException(sprintf('Router "%s" is not supported', $router::class));
		}

		return $routes;
	}

	/**
	 * @return array<mixed>
	 */
	protected function analyseRouteList(RouteList $router, ?string $module = null): array
	{
		$routes = [];

		foreach ($router->getRouters() as $subRouter) {
			if ($subRouter instanceof RouteList) {
				$routes = array_merge(
					$routes,
					$this->analyseRouteList($subRouter, $module . $router->getModule())
				);
			} elseif ($subRouter instanceof Route) {
				$routes = array_merge(
					$routes,
					[(array) $this->analyseRoute($subRouter, $module . $router->getModule())]
				);
			} else {
				throw new LogicalException(sprintf('Router "%s" is not supported', $router::class));
			}
		}

		return $routes;
	}

	/**
	 * @return stdClass
	 */
	protected function analyseRoute(Route $router, ?string $module = null): object
	{
		return (object) [
			'mask' => $router->getMask(),
			'module' => rtrim((string) $module, ':'),
			'defaults' => $this->analyseDefaults($router->getDefaults()),
			'class' => $router::class,
		];
	}

	/**
	 * @param string[] $defaults
	 */
	protected function analyseDefaults(array $defaults): string
	{
		$primary = [];

		if (isset($defaults['presenter'])) {
			$primary[] = $defaults['presenter'];
			unset($defaults['presenter']);
		}

		if (isset($defaults['action'])) {
			$primary[] = $defaults['action'];
			unset($defaults['action']);
		}

		if (isset($defaults['id'])) {
			$primary[] = $defaults['id'];
			unset($defaults['id']);
		}

		$secondary = [];

		foreach ($defaults as $key => $value) {
			$secondary[] = sprintf('%s=>%s', $key, $value);
		}

		if ($secondary !== []) {
			return implode(':', $primary) . ' [' . implode(',', $secondary) . ']';
		}

		return implode(':', $primary);
	}

}
