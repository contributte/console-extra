<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Cache\Cleaners;

use Nette\Caching\Cache;
use Nette\Caching\Storage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NetteCachingStorageCleaner implements ICleaner
{

	/**
	 * @param Storage[] $storages
	 */
	public function __construct(
		private readonly array $storages,
	)
	{
	}

	public function getDescription(): string
	{
		return Storage::class;
	}

	public function clean(InputInterface $input, OutputInterface $output): bool
	{
		if ($this->storages === []) {
			$output->writeln(sprintf('<comment>Skipped %s cleaning, no IStorage services defined.</comment>', Storage::class));

			return false;
		}

		$output->writeln(sprintf('<comment>Cleaning %s</comment>', Storage::class));

		foreach ($this->storages as $name => $storage) {
			$output->writeln(sprintf('Cleaning storage instance %s', (string) $name), OutputInterface::VERBOSITY_VERBOSE);
			$storage->clean([
				Cache::All => true,
			]);
		}

		$output->writeln(sprintf('<info>%s successfully cleaned.</info>', Storage::class));

		return true;
	}

}
