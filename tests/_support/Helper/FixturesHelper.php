<?php declare(strict_types=1);
namespace Helper;

use Codeception\TestInterface;
use Helper\Module\Db;

/**
 * Fixtures module
 *
 * Enable FixturesHelper in your suite.yml file
 */
class FixturesHelper extends \Codeception\Module
{
    private string $jsonSuffix = 'fixtures.json';

    private string $arraySuffix = 'fixtures.php';

    /**
     * @var array
     */
    private array $loadedRecords = [];

    private bool $cleanup = true;

    public function _before(TestInterface $test): void
    {
        $testClass = \method_exists($test, 'getTestClass') ? $test->getTestClass() : $test;

        $this->cleanup = $testClass->cleanup ?? true;
    }

    /**
     * Removes all previously inserted records
     */
    public function _after(TestInterface $test): void
    {
        if (!$this->cleanup) {
            $this->loadedRecords = [];
            return;
        }

        try {
            foreach (\array_reverse($this->loadedRecords, true) as $key => $record) {
                $this->getDbHelperModule()->deleteFromDatabase($record['table'], $record['data']);
            }
        } catch (\PDOException $e) {
            // suppress integrity errors in case of test failure
            if ($e->getCode() !== '23000' || $test->getTestResultObject()->wasSuccessful()) {
                throw $e;
            }
        }

        $this->loadedRecords = [];
    }

    public function haveArrayFixtures(array $fixtures): void
    {
        foreach ($fixtures as $tableName => $records) {
            $this->getDbHelperModule()->assertTableExists($tableName);

            foreach ($records as $record) {
                $this->haveArrayFixture($tableName, $record);
            }
        }
    }

    /**
     * Adds a record to the database
     */
    public function haveArrayFixture(string $table, array $data): void
    {
        if ($this->cleanup) {
            $this->getDbHelperModule()->deleteFromDatabase($table, isset($data['id']) ? ['id' => $data['id']] : $data);
        }
        $this->getDbModule()->haveInDatabase($table, $data);
        $this->scheduleForRemoval($table, $data);
    }

    private function haveJsonFixtures(string $fixtures): void
    {
        $data = \json_decode($fixtures, true);
        $this->haveArrayFixtures($data);
    }

    /**
     *
     * @deprecated Please use the tests/_support/Helper/FatEntityGeneratorHelper.php methods instead
     */
    public function loadJsonFixturesFrom(string $fileName): void
    {
        $this->haveJsonFixtures($this->getJsonFixturesFrom($fileName));
    }

    /**
     * @return bool|string
     */
    public function getJsonFixturesFrom(string $fileName)
    {
        if (\strpos($fileName, '.json') !== \strlen($fileName) - \strlen('.json')) {
            $fileName = $fileName. '.' . $this->jsonSuffix;
        }

        $this->validateFileName($fileName);
        return \file_get_contents($fileName);
    }

    /**
     * @throws \InvalidArgumentException If the file does not exist.
     */
    protected function validateFileName(string $fileName): void
    {
        if (!\is_file($fileName)) {
            throw new \InvalidArgumentException(\sprintf('Fixture file %s does not exist', $fileName));
        }
    }

    protected function scheduleForRemoval(string $table, array $data): void
    {
        $this->loadedRecords[] = ['table' => $table, 'data' => isset($data['id']) ? ['id' => $data['id']] : $data];
    }

    protected function getDbHelperModule(): DbHelper
    {
        return $this->getModule('\Helper\DbHelper');
    }

    protected function getDbModule(): Db
    {
        return $this->getModule('Helper\Module\Db');
    }
}
