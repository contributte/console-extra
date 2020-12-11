<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Router;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\SimpleRouter;
use Nette\Routing\Router;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RouterDumpCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'nette:router:dump';

	/** @var Router */
	private $router;

	public function __construct(Router $router)
	{
		parent::__construct();
		$this->router = $router;
	}

	/**
	 * Configure command
	 */
	protected function configure(): void
	{
		$this->setName(self::$defaultName);
		$this->setDescription('Display all defined routes');
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
	 * @return mixed[]
	 */
	protected function createRows(): array
	{
		return $this->analyse($this->router);
	}

	/**
	 * @return mixed[]|object
	 */
	protected function analyse(Router $router, ?string $module = null)
	{
		if ($router instanceof RouteList) {
			$routes = [];

			foreach ($router as $subRouter) {
				$route = $this->analyse($subRouter, $module . $router->getModule());

				if (is_array($route)) {
					$routes = array_merge($routes, $route);
				} else {
					$routes[] = (array) $route;
				}
			}

			return $routes;
		} else {
			return (object) [
				'mask' => $router instanceof Route ? $router->getMask() : null,
				'module' => rtrim((string) $module, ':'),
				'defaults' => $router instanceof Route || $router instanceof SimpleRouter ? $this->analyseDefaults($router->getDefaults()) : null,
				'class' => get_class($router),
			];
		}
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
