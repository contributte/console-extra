<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Caching;

use Contributte\Console\Extra\Utils\Utils;
use Nette\Caching\Cache;
use Nette\Caching\Storage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'nette:caching:clear',
	description: 'Clear Nette Caching Storage',
)]
class CachingClearCommand extends Command
{

	private Storage $storage;

	public function __construct(Storage $storage)
	{
		parent::__construct();

		$this->storage = $storage;
	}

	protected function configure(): void
	{
		$this->addOption('all', null, InputOption::VALUE_OPTIONAL, 'Clear whole storage', false);
		$this->addOption('tag', 't', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Clear by tag(s)', []);
		$this->addOption('priority', 'p', InputOption::VALUE_OPTIONAL, 'Clear by priority');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Caching Clear');

		if ($input->getOption('all') === null) {
			$this->storage->clean([Cache::All => true]);
			$style->success('Clearing whole storage done.');
		} elseif ($input->getOption('tag') !== null) {
			$this->storage->clean([Cache::Tags => $input->getOption('tag')]);
			$style->listing((array) $input->getOption('tag'));
			$style->success('Clearing by tags done.');
		} elseif ($input->getOption('priority') !== null) {
			$this->storage->clean([Cache::Priority => $input->getOption('priority')]);
			$style->comment(Utils::stringify($input->getOption('priority')));
			$style->success('Clearing by priority done.');
		} else {
			$style->warning('Specify clearing strategy.');

			return 1;
		}

		return 0;
	}

}
