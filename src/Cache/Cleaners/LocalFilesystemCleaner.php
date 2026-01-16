<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Cache\Cleaners;

use Contributte\Console\Extra\Utils\Files;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LocalFilesystemCleaner implements ICleaner
{

	/**
	 * @param string[] $directories
	 * @param string[] $ignored
	 */
	public function __construct(
		private readonly array $directories,
		private readonly array $ignored = [],
	)
	{
	}

	public function getDescription(): string
	{
		return 'Local files';
	}

	public function clean(InputInterface $input, OutputInterface $output): bool
	{
		if ($this->directories === []) {
			$output->writeln('<comment>Skipped local filesystem cache cleaning, no directories defined.</comment>');

			return false;
		}

		$output->writeln('<comment>Cleaning local filesystem cache</comment>');

		foreach ($this->directories as $directory) {
			$output->writeln(sprintf('Cleaning directory %s', $directory), OutputInterface::VERBOSITY_VERBOSE);
			Files::purge($directory, $this->ignored);
		}

		$output->writeln('<info>Local filesystem cache successfully cleaned.</info>');

		return true;
	}

}
