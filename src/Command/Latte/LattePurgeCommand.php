<?php

namespace Contributte\Console\Extra\Command\Latte;

use Contributte\Console\Extra\Command\AbstractCommand;
use Contributte\Console\Extra\Utils\Files;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LattePurgeCommand extends AbstractCommand
{

	/** @var string[] */
	private $dirs;

	/**
	 * @param string[] $dirs
	 */
	public function __construct(array $dirs)
	{
		parent::__construct();
		$this->dirs = $dirs;
	}

	/**
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('nette:latte:purge');
		$this->setDescription('Clear temp/latte folder');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Latte Purge');

		foreach ($this->dirs as $directory) {
			$style->text(sprintf('Purging: %s', $directory));
			Files::purge($directory);
		}

		$style->success(sprintf('Purging done. Total %d folders purged.', count($this->dirs)));
	}

}
