<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Cache\Cleaners;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OpcodeCleaner implements ICleaner
{

	public function getDescription(): string
	{
		return 'OPCode cache';
	}

	public function clean(InputInterface $input, OutputInterface $output): bool
	{
		if (!function_exists('opcache_reset')) {
			$output->writeln('<comment>Skipped opcode cache cleaning, opcache_reset function is not available.</comment>');

			return false;
		}

		$output->writeln('<comment>Cleaning opcode cache cache</comment>');
		$success = @opcache_reset();

		if ($success) {
			$output->writeln('<info>opcode cache successfully cleaned.</info>');

			return true;
		} else {
			$output->writeln('<error>opcode cache cannot be cleaned. It is probably restricted by "restrict_api" directive of OPcache API.</error>');

			return false;
		}
	}

}
