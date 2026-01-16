<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Cache;

use Contributte\Console\Extra\Utils\Files;
use Contributte\Console\Extra\Utils\Utils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'nette:cache:purge',
	description: 'Clear temp folders and others',
)]
class CachePurgeCommand extends Command
{

	/**
	 * @param string[] $dirs
	 */
	public function __construct(
		private readonly array $dirs,
	)
	{
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->addOption('recreate', null, InputOption::VALUE_OPTIONAL, 'Recreate folders', false);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Cache Purge');

		foreach ($this->dirs as $directory) {
			$style->text(sprintf('Purging: %s', $directory));
			Files::purge($directory);

			if (Utils::boolenize($input->getOption('recreate'))) {
				$style->text(sprintf('Recreating: %s', $directory));
				Files::mkdir($directory);
			}
		}

		$style->success(sprintf('Purging done. Total %d folders purged.', count($this->dirs)));

		return 0;
	}

}
