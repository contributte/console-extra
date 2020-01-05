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
		$this->addOption('count', 'c', InputOption::VALUE_OPTIONAL, '', 10);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Nette Random');

		$table = new Table($output);
		$table->setHeaders(['ID', 'Generated strings']);

		for ($i = 1; $i <= $input->getOption('count'); $i++) {
			$table->addRow([$i, Random::generate(50)]);

			if ($i !== $input->getOption('count')) {
				$table->addRow(new TableSeparator());
			}
		}

		$table->render();
		$style->success(sprintf('Total generated strings %d.', $input->getOption('count')));

		return 0;
	}

}
