<?php declare(strict_types=1);

namespace ExEss\Cms\Component\PhpCS\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetupPhpCsCommand extends Command
{
    use FileHandlingTrait;

    /**
     * @var string
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $defaultName = 'setup:phpcs';

    protected function configure(): void
    {
        $this
            ->setDescription('Sets up phpcs for your project')
            ->addOption(
                'root',
                'root',
                InputOption::VALUE_OPTIONAL,
                'Path to root folder',
                '.'
            )
            ->addOption(
                'custom',
                'cust',
                InputOption::VALUE_OPTIONAL,
                'Name of the application',
                null
            )
            ->addOption(
                'save-path',
                'save',
                InputOption::VALUE_OPTIONAL,
                'Path in which to save phpcs files',
                '/config'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Prepare PhpCS by copying config files');

        $this->setup($input->getOption('root'), $input->getOption('save-path'), $input->getOption('custom'));

        $io->success('PhpCS set up!');
        return 0;
    }

    private function setup(
        string $root,
        string $savePath,
        ?string $custom
    ): void {
        $baseConfigPath = __DIR__ . '/../Template/';

        $fileSystem = $this->getFileSystem();
        $customContents = '';
        if ($custom !== null && $fileSystem->exists($root . $custom)) {
            $customContents = \file_get_contents($root . $custom);
        }

        $fileSystem->copy(
            $baseConfigPath . 'phpcs.dist.xml',
            $root . $savePath . '/phpcs.xml',
            true
        );

        $this->replaceContent(
            $root . $savePath . '/phpcs.xml',
            [
                '{{project.custom}}' => $customContents,
            ]
        );
        $this->replaceContent(
            $root . $savePath . '/phpcs.xml',
            [
                '{{project.basedir}}' => $root,
                '{{custom.sniffs}}' => \dirname(__DIR__) . '/Sniffs/',
            ]
        );
    }
}
