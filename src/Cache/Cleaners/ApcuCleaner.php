<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Cache\Cleaners;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApcuCleaner implements ICleaner
{

	public function getDescription(): string
	{
		return 'APCu cache';
	}

	public function clean(InputInterface $input, OutputInterface $output): bool
	{
		if (!function_exists('apcu_clear_cache')) {
			$output->writeln('<comment>Skipped APCu cache cleaning, apcu_clear_cache function is not available.</comment>');

			return false;
		}

		$output->writeln('<comment>Cleaning APCu cache</comment>');

		apcu_clear_cache();

		$output->writeln('<info>APCu cache successfully cleaned.</info>');

		return true;
	}

}
