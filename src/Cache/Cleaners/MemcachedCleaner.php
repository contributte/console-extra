<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Cache\Cleaners;

use Memcache;
use Memcached;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MemcachedCleaner implements ICleaner
{

	/** @var Memcached[]|Memcache[] */
	private $memcaches;

	/**
	 * @param Memcached[]|Memcache[] $memcaches
	 */
	public function __construct(array $memcaches)
	{
		$this->memcaches = $memcaches;
	}

	public function getDescription(): string
	{
		return 'Memcached';
	}

	public function clean(InputInterface $input, OutputInterface $output): bool
	{
		if ($this->memcaches === []) {
			$output->writeln('<comment>Skipped Memcache(d) cleaning, no Memcache(d) services defined.</comment>');

			return false;
		}

		$output->writeln('<comment>Cleaning Memcache(d)</comment>');

		foreach ($this->memcaches as $name => $memcache) {
			$output->writeln(sprintf('Cleaning Memcache(d) instance %s', (string) $name), OutputInterface::VERBOSITY_VERBOSE);
			$memcache->flush();
		}

		$output->writeln('<info>Memcache(d) successfully cleaned.</info>');

		return true;
	}

}
