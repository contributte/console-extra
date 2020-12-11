<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Database;

use Contributte\Console\Extra\Utils\Database;
use Contributte\Console\Extra\Utils\Utils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BackupCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'contributte:database:backup';

	/** @var string */
	private $backupPath;

	public function __construct(string $backupPath = '')
	{
		parent::__construct();
		$this->backupPath = $backupPath;
	}

	protected function configure(): void
	{
		$this->setName(self::$defaultName)
			->setDescription('Dump database to defined file and path, this command internally use "mysqldump" or "pg_dump" and "gzip" and can not work without these installed binaries');

		$this->addArgument('platform', InputArgument::REQUIRED, 'mysql|postgresql')
			->addArgument('host', InputArgument::REQUIRED, 'SQL server IP')
			->addArgument('port', InputArgument::REQUIRED, 'SQL server port')
			->addArgument('username', InputArgument::REQUIRED, 'SQL server username')
			->addArgument('password', InputArgument::REQUIRED, 'SQL server password')
			->addArgument('database', InputArgument::REQUIRED, 'Database name')
			->addArgument('path', InputArgument::OPTIONAL, 'where save backup file (backup is saved to path defined in configuration if not defined)')
			->addArgument('filename', InputArgument::OPTIONAL, 'backup filename (generated automatically if not defined)')
			->addOption('no-gzip', 'g', InputOption::VALUE_NONE, 'do not gzip result')
			->addOption('bin-path', 'b', InputOption::VALUE_OPTIONAL, 'path to mysqldump or pg_dump binary');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		// Gzip compression
		$gzip = !(bool) $input->getOption('no-gzip');

		if (!$gzip) {
			$output->writeln('<info>Gzip compression is disabled</info>');
		} elseif (!$this->isGzipEnabled()) {
			$output->writeln('<error>Error: gzip binary not found, use "--no-gzip" option</error>');

			return 1;
		}

		// FileName
		/** @var string|null $filename */
		$filename = $input->getArgument('filename');
		/** @var string $database */
		$database = $input->getArgument('database');

		if ($filename === null || $filename === '') {
			$filename = $database . '.backup.' . date('d-m-Y-h-i') . (!$gzip ? '.sql' : '.gz');
		} elseif ($gzip && !Database::isGz($filename)) {
			$output->writeln('<error>Error: expected ".gz" filename extension</error>');

			return 1;
		} elseif (!$gzip && !Database::isSql($filename)) {
			$output->writeln('<error>Error: expected ".sql" filename extension</error>');

			return 1;
		}

		// Path
		/** @var string|null $path */
		$path = $input->getArgument('path');

		if ($path === null || $path === '') {
			$path = $this->backupPath;
		} elseif (!is_dir($path)) {
			$output->writeln('<error>Error: given path "' . $path . '" was not found</error>');

			return 1;
		}

		$path = rtrim($path, DIRECTORY_SEPARATOR);

		// Destination
		$backupDestination = escapeshellarg($path . DIRECTORY_SEPARATOR . $filename);

		// Normalize bin-path
		$binPath = Utils::stringify($input->getOption('bin-path'));
		$binPath = Database::normalizeBinPath($binPath, ['mysqldump', 'pg_dump']);

		// Create command
		/** @var string $platform */
		$platform = $input->getArgument('platform');
		/** @var string $port */
		$port = $input->getArgument('port');
		/** @var string $username */
		$username = $input->getArgument('username');
		/** @var string $password */
		$password = $input->getArgument('password');
		/** @var string $host */
		$host = $input->getArgument('host');

		if ($platform === Database::PLATFORM_MYSQL) {
			$port = $port !== '' ? '--port ' . $port : '';
			$command = $binPath . sprintf(
				'mysqldump --user %s --password=\'%s\' --host %s %s --opt %s',
				$username,
				$password,
				$host,
				$port,
				$database
			);
		} elseif ($platform === Database::PLATFORM_POSTGRES) {
			$port = $port !== '' ? ':' . $port : '';
			$command = $binPath . sprintf(
				'pg_dump --dbname=postgresql://%s:%s@%s%s/%s --blobs --no-owner',
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

		if ($gzip) {
			$command .= ' | gzip -c';
		}

		$command .= ' > ' . $backupDestination;

		// Execute
		$output->writeln('<info>Backing up database "' . $database . '"...</info>');
		exec($command, $retParams, $retVal);

		if ($retVal === 0) {
			if (file_exists($backupDestination)) {
				$output->writeln('<info>Backup created, see "' . $backupDestination . '" for result</info>');

				if (filesize($backupDestination) < 50) {
					$output->writeln('<warning>Warning: created backup is empty</warning>');
				}

				return 0;
			}

			$output->writeln('<error>Error: backup was not created</error>');
		}

		return 1;
	}

	public function isGzipEnabled(): bool
	{
		exec('which gzip > /dev/null', $retParams, $retVal);

		return $retVal === 0;
	}

}
