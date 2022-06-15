<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Utils;

use Nette\Utils\Random;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UtilsRandomCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'nette:utils:random';

	protected function configure(): void
	{
		$this->setName(static::$defaultName);
		$this->setDescription('Generates random string(s) using Nette Random');
		$this->addOption('count', 'c', InputOption::VALUE_OPTIONAL, '', '10');
		$this->addOption('length', 'l', InputOption::VALUE_OPTIONAL, '', '50');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Nette Random');

		$table = new Table($output);
		$table->setHeaders(['ID', 'Generated strings']);

		$count = max(intval($input->getOption('count')), 1);
		$length = max(intval($input->getOption('length')), 1);
		for ($i = 1; $i <= $count; $i++) {
			$table->addRow([$i, Random::generate($length)]);

			if ($i !== $count) {
				$table->addRow(new TableSeparator());
			}
		}

		$table->render();
		$style->success(sprintf('Total generated strings %d.', $count));

		return 0;
	}

}
