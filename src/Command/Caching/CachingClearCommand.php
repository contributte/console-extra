<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Caching;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CachingClearCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'nette:caching:clear';

	/** @var IStorage */
	private $storage;

	public function __construct(IStorage $storage)
	{
		parent::__construct();
		$this->storage = $storage;
	}

	protected function configure(): void
	{
		$this->setName(static::$defaultName);
		$this->setDescription('Clear Nette Caching Storage');
		$this->addOption('all', null, InputOption::VALUE_OPTIONAL, 'Clear whole storage', false);
		$this->addOption('tag', 't', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Clear by tag(s)', []);
		$this->addOption('priority', 'p', InputOption::VALUE_OPTIONAL, 'Clear by priority');
	}

	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Caching Clear');

		if ($input->getOption('all') === null) {
			$this->storage->clean([Cache::ALL => true]);
			$style->success('Clearing whole storage done.');
		} elseif ($input->getOption('tag')) {
			$this->storage->clean([Cache::TAGS => $input->getOption('tag')]);
			$style->listing($input->getOption('tag'));
			$style->success('Clearing by tags done.');
		} elseif ($input->getOption('priority')) {
			$this->storage->clean([Cache::PRIORITY => $input->getOption('priority')]);
			$style->comment($input->getOption('priority'));
			$style->success('Clearing by priority done.');
		} else {
			$style->warning('Specify clearing strategy.');
			return;
		}
	}

}
