<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Database;

use Contributte\Console\Extra\Utils\Database;
use Contributte\Console\Extra\Utils\Utils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'contributte:database:load';

	protected function configure(): void
	{
		$this->setName(self::$defaultName)
			->setDescription('Import given .sql or .gz file into database, this command internally use \'mysql\' or \'psql\' and \'gunzip\' and can not work without these installed binaries');

		$this->addArgument('platform', InputArgument::REQUIRED, 'mysql|postgresql')
			->addArgument('host', InputArgument::REQUIRED, 'SQL server IP')
			->addArgument('port', InputArgument::REQUIRED, 'SQL server port')
			->addArgument('username', InputArgument::REQUIRED, 'SQL server username')
			->addArgument('password', InputArgument::REQUIRED, 'SQL server password')
			->addArgument('database', InputArgument::REQUIRED, 'Database name')
			->addArgument('filename', InputArgument::REQUIRED, 'Full path to imported file')
			->addOption('bin-path', 'b', InputOption::VALUE_OPTIONAL, 'Path to mysql or psql binary');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		// Check given file
		/** @var string $filename */
		$filename = $input->getArgument('filename');

		if (!file_exists($filename)) {
			$output->writeln('<error>Error: file "' . $filename . '" not found</error>');

			return 1;
		}

		// Setup gunzip
		if (Database::isSql($filename)) {
			$packed = false;
		} elseif (Database::isGz($filename)) {
			$packed = true;

			if (!$this->isGunzipEnabled()) {
				$output->writeln('<error>Error: gunzip binary not found</error>');

				return 1;
			}
		} else {
			$output->writeln('<error>Error: unsupported file format, expected .sql or .gz file</error>');

			return 1;
		}

		// Destination
		$filename = escapeshellarg($filename);

		// Normalize binPath
		$binPath = Utils::stringify($input->getOption('bin-path'));
		$binPath = Database::normalizeBinPath($binPath, ['mysql', 'psql']);

		// Create command
		/** @var string $platform */
		$platform = $input->getArgument('platform');
		/** @var string $port */
		$port = $input->getArgument('port');
		/** @var string $username */
		$username = $input->getArgument('username');
		/** @var string $password */
		$password = $input->getArgument('password');
		/** @var string $database */
		$database = $input->getArgument('database');
		/** @var string $host */
		$host = $input->getArgument('host');

		if ($platform === Database::PLATFORM_MYSQL) {
			$port = $port !== '' ? '--port ' . $port : '';
			$command = $binPath . sprintf(
				'mysql --user %s --password=\'%s\' --host %s %s %s',
				$username,
				$password,
				$host,
				$port,
				$database
			);
		} elseif ($platform === Database::PLATFORM_POSTGRES) {
			$port = $port !== '' ? ':' . $port : '';
			$command = $binPath . sprintf(
				'psql --dbname=postgresql://%s:%s@%s%s/%s',
				$username,
				$password,
				$host,
				$port,
				$database
			);
		} else {
			$output->writeln('<error>Error: unknown database connection type</error>');

			return 1;
		}

		if ($packed) {
			$command = 'gunzip -c ' . $filename . ' | ' . $command;
		} else {
			$command .= ' < ' . $filename;
		}

		// Execute command
		$output->writeln('<info>Importing data into database "' . $database . '"...</info>');
		exec($command, $retParams, $retVal);

		if ($retVal === 0) {
			$output->writeln('<info>Import finished</info>');

			return 0;
		}

		return 1;
	}

	public function isGunzipEnabled(): bool
	{
		exec('which gunzip > /dev/null', $retParams, $retVal);

		return $retVal === 0;
	}

}
