<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Cache\Cleaners;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface ICleaner
{

	public function getDescription(): string;

	public function clean(InputInterface $input, OutputInterface $output): bool;

}
