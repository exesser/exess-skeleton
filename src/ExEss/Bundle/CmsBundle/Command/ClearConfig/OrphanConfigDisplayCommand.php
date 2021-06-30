<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Command\ClearConfig;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrphanConfigDisplayCommand extends AbstractClearConfigCommand
{
    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $defaultName = 'config:orphan:display';

    protected function configure(): void
    {
        $this->setDescription('Display orphan config');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->displayRecords($this->clearConfigRepository->findAllOrphanConfigIds());

        return 0;
    }
}
