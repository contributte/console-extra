<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Cache\Generators;

use Latte\Engine;
use Nette\Utils\Finder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class LatteTemplatesCacheGenerator implements IGenerator
{

	/** @var Engine */
	private $latte;

	/** @var string[] */
	private $dirs;

	/** @var string[] */
	private $excludeDirs;

	/**
	 * @param string[] $dirs
	 * @param string[] $excludeDirs
	 */
	public function __construct(Engine $latte, array $dirs, array $excludeDirs = [])
	{
		$this->latte = $latte;
		$this->dirs = $dirs;
		$this->excludeDirs = $excludeDirs;
	}

	public function getDescription(): string
	{
		return 'Latte templates cache';
	}

	public function generate(InputInterface $input, OutputInterface $output): bool
	{
		if ($this->dirs === []) {
			$output->writeln('<comment>Latte templates compilation skipped, no directories defined.</comment>');
			return false;
		}

		$output->writeln('Compiling Latte templates...');

		$finder = Finder::findFiles('*.latte')->from($this->dirs);
		if ($this->excludeDirs !== []) $finder->exclude($this->excludeDirs);

		$successes = 0;
		$fails = 0;
		foreach ($finder as $path => $file) {
			$path = realpath($path);

			$output->writeln(sprintf('Compiling %s...', $path), OutputInterface::VERBOSITY_VERBOSE);

			try {
				$this->latte->warmupCache($path);
				$successes++;
			} catch (Throwable $e) {
				$output->writeln(sprintf('<error>Failed %s compilation.</error>', $path), OutputInterface::VERBOSITY_VERBOSE);
				$fails++;
			}
		}

		if ($fails !== 0) {
			$output->writeln(sprintf('<info>%d templates compiled,</info> <error>compilation of %d files failed.</error>', $successes, $fails));
			return false;
		} else {
			$output->writeln('<info>All templates successfully compiled.</info>');
			return true;
		}
	}

}
