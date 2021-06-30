<?php
namespace ExEss\Bundle\CmsBundle\Robo\Task\Db\Release;

use PDO;
use Robo\Result;
use ExEss\Bundle\CmsBundle\Robo\Task\Db\AbstractDb;
use ExEss\Bundle\CmsBundle\Robo\Task\Db\DumpToDatabase;
use ExEss\Bundle\CmsBundle\Robo\Task\Db\Fix\UserIds;
use ExEss\Bundle\CmsBundle\Robo\Task\Db\Release\Export\DwpConfig;
use ExEss\Bundle\CmsBundle\Robo\Task\Db\Release\Export\Translations;
use ExEss\Bundle\CmsBundle\Robo\Task\Db\SqlFileImport;

class Exporter extends AbstractDb
{
    private const TEMP_DB = 'tmp_integrity';
    private const SCRIPT_DIR = __DIR__ . '/../../../../../../dev/database';

    private bool $forceExportAll = false;

    public function setForceExportAll(bool $forceExportAll = true): self
    {
        $this->forceExportAll = $forceExportAll;

        return $this;
    }

    public function run(): Result
    {
        if ($this->forceExportAll) {
            return $this->exportFrom($this->getDatabase());
        }

        $this->output()->newLine(5);
        $this->output()->title("Prepare check db: " . self::TEMP_DB);

        $ignoredTables = '';
        $qry = "SELECT TABLE_NAME FROM information_schema.TABLES "
            . "WHERE TABLE_SCHEMA = '" . $this->getDatabase() . "' and TABLE_TYPE = 'VIEW'";
        $dbh = $this->getPdoConnection($this->getDatabase());
        foreach ($dbh->query($qry)->fetchAll(PDO::FETCH_ASSOC) as ['TABLE_NAME' => $tableName]) {
            $ignoredTables .= " --ignore-table=" . $this->getDatabase() . ".$tableName";
        }

        (new SqlFileImport($this->output(), [self::SCRIPT_DIR . '/clean-full-db.sql'], self::TEMP_DB))->run();

        (
            new DumpToDatabase(
                $this->output(),
                "-d $ignoredTables",
                [],
                $this->getDatabase(),
                self::TEMP_DB,
                "grep -v \"ALGORITHM=UNDEFINED\""
            )
        )->run();

        $this->output()->newLine(5);
        $this->output()->title("Setup " . \User::USERNAME_ADMIN . " user in " . self::TEMP_DB);
        $this
            ->getPdoConnection(self::TEMP_DB)
            ->query(
                "INSERT INTO users "
                . "SET id='1', user_name='" . \User::USERNAME_ADMIN . "', date_entered=NOW(), created_by='1'"
            );

        (
            new DumpToDatabase(
                $this->output(),
                '',
                ['securitygroups'],
                $this->getDatabase(),
                self::TEMP_DB
            )
        )->run();

        $this->output()->newLine(5);
        $this->output()->title("Export from real DB.");
        $this->exportFrom($this->getDatabase());

        $this->output()->newLine(5);
        $this->output()->title("Import config into check db: " . self::TEMP_DB);
        (new Importer($this->output(), self::TEMP_DB))->run();

        $this->output()->newLine(5);
        $this->output()->title("Fix user IDs in " . self::TEMP_DB);
        (new UserIds($this->output(), self::TEMP_DB))->run();

        $this->output()->newLine(5);
        $this->output()->title("Clean config in " . self::TEMP_DB);

        (new SqlFileImport($this->output(), [self::SCRIPT_DIR . '/clean-config.sql'], self::TEMP_DB))->run();

        $this->output()->newLine(5);
        $this->output()->title("Export from check DB");

        return $this->exportFrom(self::TEMP_DB);
    }

    public function exportFrom(string $database): Result
    {
        (new DwpConfig($this->output(), $database))
            ->setForceExportAll($this->forceExportAll)
            ->run();
        return (new Translations($this->output(), $database))
            ->setForceExportAll($this->forceExportAll)
            ->run();
    }
}
