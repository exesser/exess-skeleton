<?php
namespace ExEss\Cms\Robo\Task\Db\Fix;

use Robo\Result;
use ExEss\Cms\Robo\Task\Db\AbstractDb;

class UserIds extends AbstractDb
{
    public function run(): Result
    {
        try {
            $this->handle('created_by', '1');
            $this->handle('modified_user_id');
            $this->handle('assigned_user_id');
        } catch (\Throwable $e) {
            return new Result($this, Result::EXITCODE_ERROR, $e->getMessage());
        }

        return new Result($this, Result::EXITCODE_OK);
    }

    /**
     * @throws \LogicException In case the update statement fails.
     */
    private function handle(string $field, ?string $toUserId = null): void
    {
        $databaseName = $this->getDatabase();
        $db = $this->getPdoConnection($databaseName);
        $newUserId = $toUserId === null ? 'NULL' : "'$toUserId'";

        $query = <<<SQL
            SELECT DISTINCT TABLES.TABLE_NAME FROM information_schema.COLUMNS 
                inner join information_schema.TABLES
                    on TABLES.TABLE_NAME = COLUMNS.TABLE_NAME
            WHERE COLUMNS.TABLE_SCHEMA = '$databaseName' 
              AND COLUMNS.TABLE_NAME NOT LIKE '%_aud' 
              AND COLUMNS.COLUMN_NAME = '$field'
              AND TABLES.TABLE_TYPE = 'BASE TABLE'
SQL;

        foreach ($db->query($query)->fetchAll() as $row) {
            try {
                $table = $row['TABLE_NAME'];
                $qry = "UPDATE $table SET $field = $newUserId WHERE $field IS NOT NULL AND $field  <> '1'";

                if ($changedRows = $db->query($qry)->rowCount()) {
                    $this->output()->text(
                        "Fixed user ids in $changedRows records for $field in $table, remapped to user id $newUserId"
                    );
                }
            } catch (\Throwable $e) {
                $this->output()->writeln("Query failed: " . $qry . ", reason: " . $e->getMessage());
                throw $e;
            }
        }
    }
}
