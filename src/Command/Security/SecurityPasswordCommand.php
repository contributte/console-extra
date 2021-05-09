<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Security;

use Nette\Security\Passwords;
use Nette\Utils\Random;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SecurityPasswordCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'nette:security:password';

	/** @var Passwords */
	private $passwords;

	public function __construct(Passwords $passwords)
	{
		parent::__construct();
		$this->passwords = $passwords;
	}

	protected function configure(): void
	{
		$this->setName(static::$defaultName);
		$this->setDescription('Generates password (s) using Nette Passwords');
		$this->addArgument('password', InputArgument::OPTIONAL, 'Given password');
		$this->addOption('count', 'c', InputOption::VALUE_OPTIONAL, '', '10');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Security Password');

		if ($input->getArgument('password') !== null) {
			// Generate one password
			$password = $input->getArgument('password');
			$style->comment('Password given');
			$encrypted = $this->passwords->hash(strval($password));
			$style->success(sprintf('Hashed password: %s', $encrypted));

			return 0;
		} else {
			// Generate more passwords
			$table = new Table($output);
			$table->setHeaders(['ID', 'Generated random password']);

			for ($i = 1; $i <= $input->getOption('count'); $i++) {
				$table->addRow([$i, $this->passwords->hash(sha1(Random::generate(50) . time() . random_bytes(20)))]);

				if ($i !== intval($input->getOption('count'))) {
					$table->addRow(new TableSeparator());
				}
			}

			$table->render();
			$style->success(sprintf('Total generated and hashed passwords %d.', intval($input->getOption('count'))));

			return 0;
		}
	}

}
