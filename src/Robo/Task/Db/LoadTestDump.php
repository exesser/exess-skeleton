<?php
namespace App\Robo\Task\Db;

use Robo\Result;
use App\Robo\Task\Db\Release\Importer;

class LoadTestDump extends AbstractDb
{
    public function run(): Result
    {
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
