<?php

namespace Contributte\Console\Extra\Command\Router;

use Contributte\Console\Extra\Command\AbstractCommand;
use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\SimpleRouter;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RouterDumpCommand extends AbstractCommand
{

	/** @var IRouter */
	private $router;

	/**
	 * @param IRouter $router
	 */
	public function __construct(IRouter $router)
	{
		parent::__construct();
		$this->router = $router;
	}

	/**
	 * Configure command
	 *
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('nette:router:dump');
		$this->setDescription('Display all defined routes');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$table = new Table($output);
		$table
			->setHeaders(['Mask', 'Module', 'Defaults', 'Router'])
			->setRows($this->createRows());

		$table->render();
	}

	/**
	 * @return array
	 */
	protected function createRows()
	{
		return $this->analyse($this->router);
	}

	/**
	 * @param IRouter $router
	 * @param null $module
	 * @return array|object
	 */
	protected function analyse(IRouter $router, $module = NULL)
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
				'mask' => $router instanceof Route ? $router->getMask() : NULL,
				'module' => rtrim($module, ':'),
				'defaults' => $router instanceof Route || $router instanceof SimpleRouter ? $this->analyseDefaults($router->getDefaults()) : NULL,
				'class' => get_class($router),
			];
		}
	}

	/**
	 * @param array $defaults
	 * @return string
	 */
	protected function analyseDefaults(array $defaults)
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

		if ($secondary) {
			return implode(':', $primary) . ' [' . implode(',', $secondary) . ']';
		}

		return implode(':', $primary);
	}

}
