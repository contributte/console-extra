<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Latte;

use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Nette\Utils\Finder;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class LatteWarmupCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'nette:latte:warmup';

	/** @var ITemplateFactory */
	private $templateFactory;

	/** @var string[] */
	private $dirs;

	/** @var string[] */
	private $excludeDirs;

	/**
	 * @param string[] $dirs
	 * @param string[] $excludeDirs
	 */
	public function __construct(TemplateFactory $templateFactory, array $dirs, array $excludeDirs = [])
	{
		parent::__construct();
		$this->templateFactory = $templateFactory;
		$this->dirs = $dirs;
		$this->excludeDirs = $excludeDirs;
	}

	protected function configure(): void
	{
		$this->setName(static::$defaultName);
		$this->setDescription('Warmup Latte templates (*.latte)');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Latte Warmup');

		/** @var Template $template */
		$template = $this->templateFactory->createTemplate();
		$latte = $template->getLatte();

		$finder = Finder::findFiles('*.latte')->from($this->dirs);

		if ($this->excludeDirs !== []) {
			$finder->exclude($this->excludeDirs);
		}

		$stats = ['ok' => 0, 'error' => 0];

		/** @var SplFileInfo $file */
		foreach ($finder as $path => $file) {
			try {
				$latte->warmupCache($file->getPathname());
				$stats['ok']++;

				if ($output->isVerbose()) {
					$style->text(sprintf('Warmuping: %s', $file->getPathname()));
				}
			} catch (Throwable $e) {
				$stats['error']++;

				if ($output->isVerbose()) {
					$style->caution(sprintf("Warmuping error: %s\nError: %s", $file->getPathname(), $e->getMessage()));
				}
			}
		}

		if ($stats['error'] > 0) {
			$style->warning(sprintf(
				'Warmup partial done. %d success / %d errors. Total %d files.',
				$stats['ok'],
				$stats['error'],
				$stats['ok'] + $stats['error']
			));
		} else {
			$style->success(sprintf('Warmup done. Total %d files.', $stats['ok']));
		}

		return 0;
	}

}
