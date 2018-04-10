<?php

namespace Contributte\Console\Extra\Command\Caching;

use Contributte\Console\Extra\Command\AbstractCommand;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CachingClearCommand extends AbstractCommand
{

	/** @var IStorage */
	private $storage;

	/**
	 * @param IStorage $storage
	 */
	public function __construct(IStorage $storage)
	{
		parent::__construct();
		$this->storage = $storage;
	}


	/**
	 * @return void
	 */
	protected function configure()
	{
		$this->setName('nette:caching:clear');
		$this->setDescription('Clear Nette Caching Storage');
		$this->addOption('all', NULL, InputOption::VALUE_OPTIONAL, 'Clear whole storage', FALSE);
		$this->addOption('tag', 't', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Clear by tag(s)', []);
		$this->addOption('priority', 'p', InputOption::VALUE_OPTIONAL, 'Clear by priority');
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Caching Clear');

		if ($input->getOption('all') === NULL) {
			$this->storage->clean([Cache::ALL => TRUE]);
			$style->success('Clearing whole storage done.');
		} else if ($input->getOption('tag')) {
			$this->storage->clean([Cache::TAGS => $input->getOption('tag')]);
			$style->listing($input->getOption('tag'));
			$style->success('Clearing by tags done.');
		} else if ($input->getOption('priority')) {
			$this->storage->clean([Cache::PRIORITY => $input->getOption('priority')]);
			$style->comment($input->getOption('priority'));
			$style->success('Clearing by priority done.');
		} else {
			$style->warning('Specify clearing strategy.');
			return;
		}
	}

}
