<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Cache\Generators;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface IGenerator
{

	public function getDescription(): string;

	public function generate(InputInterface $input, OutputInterface $output): bool;

}
