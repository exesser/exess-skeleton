<?php
namespace ExEss\Cms\Robo\Task\Db\Release;

use Robo\Result;
use ExEss\Cms\CRUD\Config\ConfigurationTask;
use ExEss\Cms\Robo\Task\Db\AbstractDb;
use ExEss\Cms\Robo\Task\Db\Release\Import\Translations;
use ExEss\Cms\Robo\Task\Db\SqlFileImport;

class Importer extends AbstractDb
{
    public function run(): Result
    {
        // import translations
        $result = (new Translations($this->output(), $this->getDatabase()))->run();
        if ($result->getExitCode() !== 0) {
            return $result;
        }

        // import general DWP config
        $result = (
            new SqlFileImport(
                $this->output(),
                \glob('dev/database/releases/config/*'),
                $this->getDatabase()
            )
        )->run();
        if ($result->getExitCode() !== 0) {
            return $result;
        }

        // import CRUD from repository (overwriting what might be in the config dump, repository has preference)
        $result = (new ConfigurationTask($this->output(), $this->getDatabase()))->run();
        if ($result->getExitCode() !== 0) {
            return $result;
        }

        // last, clear the cache
        $this->taskExec('APP_ENV=' . $_ENV['APP_ENV'] ?? 'prod' . ' php bin/console nova:cache:clear')->run();

        return new Result($this, Result::EXITCODE_OK);
    }
}
