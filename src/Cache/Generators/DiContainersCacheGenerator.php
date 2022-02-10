<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Cache\Generators;

use Nette\Configurator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiContainersCacheGenerator implements IGenerator
{

	/** @var mixed[] */
	private $config;

	/** @var Configurator */
	private $configurator;

	/**
	 * @param mixed[] $config
	 */
	public function __construct(array $config, Configurator $configurator)
	{
		$this->config = $config;
		$this->configurator = $configurator;
	}

	public function getDescription(): string
	{
		return 'DI Containers cache';
	}

	public function generate(InputInterface $input, OutputInterface $output): bool
	{
		if ($this->config === []) {
			$output->writeln('<comment>Containers generating skipped, no containers configuration defined.</comment>');

			return false;
		}

		$output->writeln('<comment>Compiling DI containers</comment>');

		/** @var mixed[] $parameters */
		foreach ($this->config as $container => $parameters) {
			if (isset($parameters['debugMode'])) { // Nette BC
				$parameters['productionMode'] = !boolval($parameters['debugMode']);
			}

			$output->writeln(sprintf(
				'Compiling container `%s`',
				(string) $container
			));

			$configurator = clone $this->configurator;
			$configurator->addParameters($parameters);
			$configurator->loadContainer();
		}

		$output->writeln('<info>All containers successfully generated.</info>');

		return true;
	}

}
