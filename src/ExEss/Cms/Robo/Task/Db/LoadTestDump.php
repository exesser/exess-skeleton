<?php
namespace ExEss\Cms\Robo\Task\Db;

use Robo\Result;
use ExEss\Cms\Robo\Task\Db\Release\Importer;

class LoadTestDump extends AbstractDb
{
    public function run(): Result
    {
        // drop database
        $result = $this
            ->taskExec('php bin/console doctrine:database:drop --force --if-exists --env=' . $_ENV['APP_ENV'])
            ->run();
        if (!$result->wasSuccessful()) {
            return $result;
        }
        // create database
        $result = $this
            ->taskExec('php bin/console doctrine:database:create --env=' . $_ENV['APP_ENV'])
            ->run();
        if (!$result->wasSuccessful()) {
            return $result;
        }
        // run migrations
        $result = $this
            ->taskExec('php bin/console doctrine:migrations:migrate --no-interaction --env=' . $_ENV['APP_ENV'])
            ->run();
        if (!$result->wasSuccessful()) {
            return $result;
        }

        // import the test dump (prod leading config) and fixtures
        $mysqlImportCommand = $this->wrapForCliPipe(
            '{ cat '
            . 'tests/_data/sql/fixtures.sql '
            . 'tests/_data/sql/acl_actions.sql '
            . 'tests/_data/sql/acl_roles_actions.sql '
            . 'tests/_data/sql/securitygroups_api.sql '
            . '; }',
            true,
            $this->getDatabase()
        );
        $result = $this->taskExec($mysqlImportCommand)->run();
        if (!$result->wasSuccessful()) {
            return $result;
        }

        // import the config
        return (new Importer($this->output(), $this->getDatabase()))->run();
    }
}
