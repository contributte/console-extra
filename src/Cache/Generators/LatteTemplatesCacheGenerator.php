<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Cache\Generators;

use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class LatteTemplatesCacheGenerator implements IGenerator
{

	/** @var ITemplateFactory */
	private $templateFactory;

	/** @var string[] */
	private $dirs;

	/** @var string[] */
	private $excludeDirs;

	/** @var string|null */
	private $rootDir;

	/**
	 * @param string[] $dirs
	 * @param string[] $excludeDirs
	 */
	public function __construct(ITemplateFactory $templateFactory, array $dirs, array $excludeDirs = [], ?string $rootDir = null)
	{
		$this->templateFactory = $templateFactory;
		$this->dirs = $dirs;
		$this->excludeDirs = $excludeDirs;
		$this->rootDir = $rootDir;
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

		$output->writeln('<comment>Compiling Latte templates</comment>');

		/** @var Template $template */
		$template = $this->templateFactory->createTemplate();
		$latte = $template->getLatte();

		$finder = Finder::findFiles('*.latte')->from($this->dirs);

		if ($this->excludeDirs !== []) {
			$finder->exclude($this->excludeDirs);
		}

		$successes = 0;
		$fails = 0;

		foreach ($finder as $path => $file) {
			$path = (string) realpath($path);
			$outputPath = $this->rootDir !== null && Strings::startsWith($path, $this->rootDir)
				? substr($path, mb_strlen($this->rootDir))
				: $path;

			$output->writeln(sprintf('Compiling %s', $outputPath), OutputInterface::VERBOSITY_VERBOSE);

			try {
				$latte->warmupCache($path);
				$successes++;
			} catch (Throwable $e) {
				$output->writeln(sprintf('<error>Failed %s compilation.</error>', $outputPath), OutputInterface::VERBOSITY_VERBOSE);
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
