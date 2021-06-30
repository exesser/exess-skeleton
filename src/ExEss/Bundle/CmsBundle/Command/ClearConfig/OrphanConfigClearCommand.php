<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Command\ClearConfig;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrphanConfigClearCommand extends AbstractClearConfigCommand
{
    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $defaultName = 'config:orphan:clear';

    protected function configure(): void
    {
        $this->setDescription('Clear orphan config ');
        $this->addArgument('database', InputArgument::OPTIONAL, 'The database you want to clear');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($database = $input->getArgument('database')) {
            $this->clearConfigRepository->setDatabase($database);
        }

        $this->deleteRecords($this->clearConfigRepository->findAllOrphanConfigIds());

        return 0;
    }
}
