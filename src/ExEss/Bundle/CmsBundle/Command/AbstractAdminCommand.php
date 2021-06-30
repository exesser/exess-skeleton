<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Command;

use ExEss\Bundle\CmsBundle\Command\Traits\LoginTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractAdminCommand extends Command
{
    use LoginTrait;

    protected SymfonyStyle $io;

    /**
     * Initialize the SymfonyStyle component.
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }
}
