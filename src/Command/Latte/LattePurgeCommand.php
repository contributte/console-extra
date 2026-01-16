<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Latte;

use Contributte\Console\Extra\Utils\Files;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'nette:latte:purge',
	description: 'Clear temp/latte folder',
)]
class LattePurgeCommand extends Command
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

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$style = new SymfonyStyle($input, $output);
		$style->title('Latte Purge');

		foreach ($this->dirs as $directory) {
			$style->text(sprintf('Purging: %s', $directory));
			Files::purge($directory);
		}

		$style->success(sprintf('Purging done. Total %d folders purged.', count($this->dirs)));

		return 0;
	}

}
