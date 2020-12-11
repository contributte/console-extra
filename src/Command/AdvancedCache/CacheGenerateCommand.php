<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\AdvancedCache;

use Contributte\Console\Extra\Cache\Generators\IGenerator;
use Contributte\Console\Extra\Exception\Logical\InvalidArgumentException;
use Contributte\Console\Extra\Utils\Utils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CacheGenerateCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'contributte:cache:generate';

	/** @var IGenerator[] */
	private $generators = [];

	/**
	 * @param IGenerator[] $generators
	 */
	public function __construct(array $generators)
	{
		parent::__construct();
		$this->generators = $generators;
	}

	protected function configure(): void
	{
		$this->setName(static::$defaultName);
		$this->setDescription('Generate cache');
		$this->addOption('list', 'l', InputOption::VALUE_NONE, 'List all available generators');
		$this->addOption('generator', 'g', InputOption::VALUE_REQUIRED, 'Use only one generator');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		//todo - vypsat co failnulo a co se podařilo
		$style = new SymfonyStyle($input, $output);

		if ($input->getOption('list') === true) {
			$table = new Table($output);
			$table->setStyle('box');
			$table->setHeaders(['Name', 'Description']);
			$rows = [];

			foreach ($this->generators as $name => $generator) {
				$rows[] = [$name, $generator->getDescription()];
			}

			$table->setRows($rows);
			$table->render();

			return 0;
		}

		if (($generatorName = $input->getOption('generator')) !== null) {
			if (!is_string($generatorName) || !isset($this->generators[$generatorName])) {
				throw new InvalidArgumentException(sprintf('Cannot run undefined generator "%s"', Utils::stringify($generatorName)));
			}

			$this->generators[$generatorName]->generate($input, $output);

			return 0;
		}

		if ($this->generators === []) {
			$style->error('Cache generating skipped, no generators defined.');

			return 0;
		}

		$stats = ['ok' => [], 'error' => []];

		foreach ($this->generators as $name => $generator) {
			$success = $generator->generate($input, $output);

			if ($success) {
				$stats['ok'][] = $name;
			} else {
				$stats['error'][] = $name;
			}
		}

		if ($stats['error'] !== []) {
			$style->warning(sprintf(
				'Cache generating done. %d success / %d errors. Generator%s "%s" failed.',
				count($stats['ok']),
				count($stats['error']),
				(count($stats['error']) === 1) ? '' : 's',
				implode('", "', $stats['error'])
			));
		} else {
			$style->success('Cache successfully generated.');
		}

		return 0;
	}

}
