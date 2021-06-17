<?php declare(strict_types=1);
namespace Helper;

use Codeception\Module\Symfony;
use Codeception\TestInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Helper\Module\Db;

/**
 * Additional methods for DB module
 *
 * Save this file as DbHelper.php in _support folder
 * Enable DbHelper in your suite.yml file
 * Execute `codeception build` to integrate this class in your codeception
 */
class DbHelper extends \Codeception\Module
{
    protected function getDbModule(): Db
    {
        return $this->getModule('Helper\Module\Db');
    }

    /**
     * @see https://github.com/Codeception/module-doctrine2/issues/35
     */
    public function _before(TestInterface $test): void
    {
        /** @var Symfony $symfony */
        $symfony = $this->getModule('Symfony');
        /** @var Registry $registry */
        $registry = $symfony->grabService('doctrine');
        $registry->resetManager();
    }

    public function restoreInDatabase(string $table, array $records): void
    {
        $dbh = $this->getDbModule()->_getDbh();

        foreach ($records as $record) {
            $query = 'INSERT INTO %s SET %s';
            $insert = [];
            $values = [];
            foreach ($record as $k => $v) {
                $insert[] = "$k = ?";
                $values[] = $v;
            }
            $query = \sprintf($query, $table, \implode(', ', $insert));
            $sth = $dbh->prepare($query);
            $sth->execute($values);
        }
    }

    public function deleteFromDatabase(string $table, array $criteria = []): bool
    {

        $dbh = $this->getDbModule()->_getDbh();
        $query = 'DELETE FROM %s WHERE %s';
        $where = ['1=1'];
        $paramValues = [];
        foreach ($criteria as $k => $v) {
            if (\is_array($v)) {
                $where[] = " $k IN ('".\implode("','", $v)."')";
            } else {
                $where[] = "$k = ?";
                $paramValues[] = $v;
            }
        }

        $query = \sprintf($query, $table, \implode(' AND ', $where));
        $this->debugSection('Query', $query. ' with ' . \json_encode($criteria));

        $sth = $dbh->prepare($query);

        return $sth->execute($paramValues);
    }

    public function updateFromDatabase(string $table, array $data, array $criteria = []): bool
    {
        $dbh = $this->getDbModule()->_getDbh();
        $query = 'update %s set %s' . (!empty($criteria)? ' where %s' : '');
        $params = $dataset =[];
        foreach ($criteria as $k => $v) {
            $params[] = "$k = ?";
        }
        $params = \implode(' AND ', $params);
        foreach ($data as $c => $d) {
            if ($d === null) {
                $dataset[] = "$c = NULL";
                unset($data[$c]);
            } else {
                $dataset[] = "$c = ?";
            }
        }
        $dataset = \implode(' , ', $dataset);
        $query = \sprintf($query, $table, $dataset, $params);
        $this->debugSection('Query', $query . ' with ' . \json_encode($data) . \json_encode($criteria));
        $sth = $dbh->prepare($query);

        return $sth->execute(\array_values(\array_merge($data, $criteria)));
    }

    public function executeOnDatabase(string $sql): bool
    {
        $dbh = $this->getDbModule()->_getDbh();
        $this->debugSection('Query', $sql);
        $sth = $dbh->prepare($sql);

        return $sth->execute();
    }

    public function grabAllFromDatabase(string $tableName, string $columnName = '*', array $criteria = []): array
    {
        $dbh = $this->getDbModule()->_getDbh();
        $query = "SELECT %s FROM %s";
        $query = \sprintf($query, $columnName, $tableName) . $this->generateWhereClause($criteria);
        $params = \array_values($criteria);

        $sth = $dbh->prepare($query);
        $sth->execute($params);

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function generateWhereClause(array $criteria): string
    {
        if (empty($criteria)) {
            return '';
        }

        $whereClause = \implode(' = ? AND ', \array_keys($criteria));

        return ' WHERE ' . $whereClause . ' = ?';
    }

    public function assertNumRecords(
        int $expectedRecords,
        string $table,
        array $criteria = [],
        string $message = ''
    ): void {
        $this->assertEquals(
            $expectedRecords,
            $this->getDbModule()->grabNumRecords($table, $criteria),
            $message
        );
    }

    public function assertTableExists(string $tableName): void
    {
        $dbh = $this->getDbModule()->_getDbh();
        $query = \sprintf('SHOW TABLES LIKE %s', $dbh->quote($tableName));

        $sth = $dbh->prepare($query);
        $sth->execute();

        $this->assertEquals(1, $sth->rowCount(), \sprintf('Table %s not found', $tableName));
    }

    public function emptyTable(string $table): bool
    {
        return $this->deleteFromDatabase($table);
    }

    /**
     * Truncates a table, which resets auto increment counters as well
     */
    public function truncateTable(string $table): bool
    {
        return $this->executeOnDatabase(" TRUNCATE TABLE $table");
    }
}
