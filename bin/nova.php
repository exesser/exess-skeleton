<?php

if (\file_exists(__DIR__.'/../../../autoload.php')) {
    require __DIR__ . '/../../../autoload.php';
} else {
    require __DIR__ . '/../vendor/autoload.php';
}

use Symfony\Component\Console\Application;
use ExEss\Cms\Component\PhpCS\Command\SetupPhpCsCommand;
use ExEss\Cms\Component\PhpCS\Command\SetupGitHooksCommand;

$application = new Application('Nova shared console', '1.0.0');
$application->add(new SetupPhpCsCommand());
$application->add(new SetupGitHooksCommand());
$application->run();
