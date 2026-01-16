<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\AdvancedCache;

use Contributte\Console\Extra\Cache\Cleaners\ICleaner;
use Contributte\Console\Extra\Exception\LogicalException;
use Contributte\Console\Extra\Utils\Utils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'contributte:cache:clean',
	description: 'Clean cache',
)]
class CacheCleanCommand extends Command
{

	/**
	 * @param ICleaner[] $cleaners
	 */
	public function __construct(
		private readonly array $cleaners = [],
	)
	{
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->addOption('list', 'l', InputOption::VALUE_NONE, 'List all available cleaners');
		$this->addOption('cleaner', 'c', InputOption::VALUE_REQUIRED, 'Use only one cleaner');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$style = new SymfonyStyle($input, $output);

		if ($input->getOption('list') === true) {
			$table = new Table($output);
			$table->setStyle('box');
			$table->setHeaders(['Name', 'Description']);
			$rows = [];

			foreach ($this->cleaners as $name => $cleaner) {
				$rows[] = [$name, $cleaner->getDescription()];
			}

			$table->setRows($rows);
			$table->render();

			return 0;
		}

		if (($cleanerName = $input->getOption('cleaner')) !== null) {
			if (!is_string($cleanerName) || !isset($this->cleaners[$cleanerName])) {
				throw new LogicalException(sprintf('Cannot run undefined cleaner "%s"', Utils::stringify($cleanerName)));
			}

			$this->cleaners[$cleanerName]->clean($input, $output);

			return 0;
		}

		if ($this->cleaners === []) {
			$style->error('Cache cleaning skipped, no cleaners defined.');

			return 0;
		}

		$stats = ['ok' => [], 'error' => []];

		foreach ($this->cleaners as $name => $cleaner) {
			$success = $cleaner->clean($input, $output);

			if ($success) {
				$stats['ok'][] = $name;
			} else {
				$stats['error'][] = $name;
			}
		}

		if ($stats['error'] !== []) {
			$style->warning(sprintf(
				'Cache cleaning done. %d success / %d errors. Cleaner%s "%s" failed.',
				count($stats['ok']),
				count($stats['error']),
				(count($stats['error']) === 1) ? '' : 's',
				implode('", "', $stats['error'])
			));
		} else {
			$style->success('Cache successfully cleaned.');
		}

		return 0;
	}

}
