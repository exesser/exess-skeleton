<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Codeception\Extension;

use Codeception\Events;
use Codeception\Extension;

class DatabaseMigrationExtension extends Extension
{
    public static array $events = [
        Events::SUITE_BEFORE => 'beforeSuite',
    ];

    public function beforeSuite(): void
    {
        try {
            /** @var \Codeception\Module\Symfony $symfony */
            $symfony = $this->getModule('Symfony');

            $this->writeln('Recreating the DB...');
            $symfony->runSymfonyConsoleCommand(
                'doctrine:database:drop',
                [
                    '--if-exists' => true,
                    '--force' => true,
                    '--env' => $this->getEnv(),
                ]
            );
            $symfony->runSymfonyConsoleCommand(
                'doctrine:database:create',
                [
                    '--env' => $this->getEnv(),
                ]
            );

            $this->writeln('Running Doctrine Migrations...');
            $symfony->runSymfonyConsoleCommand(
                'doctrine:migrations:migrate',
                [
                    '--no-interaction' => true,
                    '--env' => $this->getEnv(),
                ]
            );

            $this->writeln('Test database recreated');
        } catch (\Exception $e) {
            $this->writeln(
                \sprintf(
                    'An error occurred whilst rebuilding the test database: %s',
                    $e->getMessage()
                )
            );
        }
    }

    private function getEnv(): string
    {
        return $this->getModule('Symfony')->_getConfig()['environment'];
    }
}
