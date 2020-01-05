<?php declare(strict_types = 1);

namespace Contributte\Console\Extra\Command\Latte;

use Contributte\Console\Extra\Utils\Files;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LattePurgeCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'nette:latte:purge';

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

	protected function configure(): void
	{
		$this->setName(static::$defaultName);
		$this->setDescription('Clear temp/latte folder');
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
