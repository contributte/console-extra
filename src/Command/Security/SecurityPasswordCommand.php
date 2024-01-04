<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Security;

use Contributte\Console\Extra\Utils\Utils;
use Nette\Security\Passwords;
use Nette\Utils\Random;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'nette:security:password',
	description: 'Generates password (s) using Nette Passwords',
)]
class SecurityPasswordCommand extends Command
{

	private Passwords $passwords;

	public function __construct(Passwords $passwords)
	{
		parent::__construct();

		$this->passwords = $passwords;
	}

	protected function configure(): void
	{
		$this->addArgument('password', InputArgument::OPTIONAL, 'Given password');
		$this->addOption('count', 'c', InputOption::VALUE_OPTIONAL, '', '10');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Security Password');

		if ($input->getArgument('password') !== null) {
			// Generate one password
			$password = Utils::stringify($input->getArgument('password'));
			$style->comment('Password given');
			$encrypted = $this->passwords->hash($password);
			$style->success(sprintf('Hashed password: %s', $encrypted));

			return 0;
		} else {
			// Generate more passwords
			$table = new Table($output);
			$table->setHeaders(['ID', 'Generated random password']);
			$count = Utils::numerize($input->getOption('count'));

			for ($i = 1; $i <= $count; $i++) {
				$table->addRow([$i, $this->passwords->hash(sha1(Random::generate(50) . time() . random_bytes(20)))]);

				if ($i !== $count) {
					$table->addRow(new TableSeparator());
				}
			}

			$table->render();
			$style->success(sprintf('Total generated and hashed passwords %d.', $count));

			return 0;
		}
	}

}
