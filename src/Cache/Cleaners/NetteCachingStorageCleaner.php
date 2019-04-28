<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Cache\Cleaners;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NetteCachingStorageCleaner implements ICleaner
{

	/** @var IStorage[] */
	private $storages;

	/**
	 * @param IStorage[] $storages
	 */
	public function __construct(array $storages)
	{
		$this->storages = $storages;
	}

	public function getDescription(): string
	{
		return IStorage::class;
	}

	public function clean(InputInterface $input, OutputInterface $output): bool
	{
		if ($this->storages === []) {
			$output->writeln(sprintf('<comment>Skipped %s cleaning, no IStorage services defined.</comment>', IStorage::class));

			return false;
		}

		$output->writeln(sprintf('<comment>Cleaning %s</comment>', IStorage::class));

		foreach ($this->storages as $name => $storage) {
			$output->writeln(sprintf('Cleaning storage instance %s', (string) $name), OutputInterface::VERBOSITY_VERBOSE);
			$storage->clean([
				Cache::ALL => true,
			]);
		}

		$output->writeln(sprintf('<info>%s successfully cleaned.</info>', IStorage::class));

		return true;
	}

}
