<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\PhpCS\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

trait FileHandlingTrait
{
    private function getFileSystem(): Filesystem
    {
        return new Filesystem();
    }

    private function getFinder(): Finder
    {
        return new Finder();
    }

    private function replaceContent(string $file, array $arguments): void
    {
        \file_put_contents($file, \strtr(\file_get_contents($file), $arguments));
    }
}
