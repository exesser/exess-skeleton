<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Command\ClearConfig;

use ExEss\Bundle\CmsBundle\Repository\ClearConfigRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FatEntityConfigDisplayCommand extends AbstractClearConfigCommand
{
    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $defaultName = 'config:fatEntity:display';

    protected function configure(): void
    {
        $this->setName('config:fatEntity:display');
        $this->setDescription('Display the config for a main fatEntity');

        $this->addArgument(
            'fatEntity',
            InputArgument::REQUIRED,
            'FatEntity: ' . \implode(', ', ClearConfigRepository::FAT_ENTITIES_MAIN_RECORDS)
        );

        $this->addArgument('fatEntityId', InputArgument::REQUIRED, 'FatEntityId');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fatEntity = $input->getArgument('fatEntity');
        $fatEntityId = $input->getArgument('fatEntityId');

        [$safeToRemoveConfig, $usedForOthersConfig] = $this->clearConfigRepository->findAllFatEntityConfigIds(
            $fatEntity,
            $fatEntityId
        );

        $this->io->title('Safe to remove records');
        $this->displayRecords($safeToRemoveConfig);

        $this->io->newLine(3);
        $this->io->title('Records that are used by other ' . $fatEntity);
        $this->displayRecords($usedForOthersConfig);

        return 0;
    }
}
