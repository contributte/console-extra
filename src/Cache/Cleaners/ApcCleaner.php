<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Cache\Cleaners;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApcCleaner implements ICleaner
{

	public function getDescription(): string
	{
		return 'APC cache';
	}

	public function clean(InputInterface $input, OutputInterface $output): bool
	{
		if (!function_exists('apc_clear_cache')) {
			$output->writeln('<comment>Skipped APC cache cleaning, apc_clear_cache function is not available.</comment>');

			return false;
		}

		$output->writeln('<comment>Cleaning APC cache</comment>');

		$output->writeln('Cleaning APC system cache', OutputInterface::VERBOSITY_VERBOSE);
		apc_clear_cache();

		$output->writeln('Cleaning APC user cache', OutputInterface::VERBOSITY_VERBOSE);
		apc_clear_cache('user');

		$output->writeln('<info>APC cache successfully cleaned.</info>');

		return true;
	}

}
