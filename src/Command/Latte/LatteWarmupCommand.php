<?php

namespace Contributte\Console\Extra\Command\Latte;

use Contributte\Console\Extra\Command\AbstractCommand;
use Exception;
use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Utils\Finder;
use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LatteWarmupCommand extends AbstractCommand
{

	/** @var array */
	private $dirs;

	/** @var ITemplateFactory */
	private $templateFactory;

	/**
	 * @param array $dirs
	 * @param ITemplateFactory $templateFactory
	 */
	public function __construct(array $dirs, ITemplateFactory $templateFactory)
	{
		parent::__construct();
		$this->dirs = $dirs;
		$this->templateFactory = $templateFactory;
	}

	/**
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('nette:latte:warmup');
		$this->setDescription('Warmup Latte templates (*.latte)');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Latte Warmup');

		/** @var Template $template */
		$template = $this->templateFactory->createTemplate();
		$latte = $template->getLatte();

		$finder = Finder::findFiles('*.latte')->from($this->dirs);
		$stats = ['ok' => 0, 'error' => 0];

		/** @var SplFileInfo $file */
		foreach ($finder as $path => $file) {

			try {
				$latte->warmupCache($file->getPathname());
				$stats['ok']++;

				if ($output->isVerbose()) {
					$style->text(sprintf('Warmuping: %s', $file->getPathname()));
				}
			} catch (Exception $e) {
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
	}

}
