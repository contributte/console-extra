<?php

namespace Contributte\Console\Extra\Command\Security;

use Contributte\Console\Extra\Command\AbstractCommand;
use Nette\Security\Passwords;
use Nette\Utils\Random;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SecurityPasswordCommand extends AbstractCommand
{

	/**
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('nette:security:password');
		$this->setDescription('Generates password (s) using Nette Passwords');
		$this->addArgument('password', InputArgument::OPTIONAL, 'Given password');
		$this->addOption('count', 'c', InputOption::VALUE_OPTIONAL, '', 10);
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Security Password');

		if ($input->getArgument('password')) {
			// Generate one password
			$password = $input->getArgument('password');
			$style->comment('Password given');
			$encrypted = Passwords::hash($password);
			$style->success(sprintf('Hashed password: %s', $encrypted));
		} else {
			// Generate more passwords
			$table = new Table($output);
			$table->setHeaders(['ID', 'Generated random password']);

			for ($i = 1; $i <= $input->getOption('count'); $i++) {
				$table->addRow([$i, Passwords::hash(sha1(Random::generate(50) . time() . random_bytes(20)))]);

				if ($i != $input->getOption('count')) {
					$table->addRow(new TableSeparator());
				}
			}

			$table->render();
			$style->success(sprintf('Total generated and hashed passwords %d.', $input->getOption('count')));
		}
	}

}
