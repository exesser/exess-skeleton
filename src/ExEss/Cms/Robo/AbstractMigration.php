<?php
namespace ExEss\Cms\Robo;

use Phinx\Migration\AbstractMigration as AbstractPhinxMigration;

class AbstractMigration extends AbstractPhinxMigration
{
    /**
     * Wrapper to strip out delimiter statements before executing sql
     *
     * @param $sql
     * @return int
     */
    // @codingStandardsIgnoreStart
    public function execute($sql)
    {
        // @codingStandardsIgnoreEnd
        if (\strpos($sql, 'DELIMITER ;;') !== false) {
            return parent::execute(
                \str_replace(
                    ['DELIMITER ;;', 'DELIMITER ;', ';;'],
                    ['', '', ';'],
                    $sql
                )
            );
        }

        return parent::execute($sql);
    }

    protected function fixEmptyStringsToNull(string $table, string $field): void
    {
        $this->query(<<<SQL
UPDATE $table SET $field = NULL WHERE $field = ''
SQL
        );
    }

    /**
     * utility method to remove all triggers, disconnects adapter to ensure no locking happens
     */
    protected function disableTriggers(array $patterns = []): void
    {
        $this->getAdapter()->disconnect();
        \exec(
            'bin/robo db:triggers-remove '
            . $this->getAdapter()->getOption('name') . ' '
            . \implode(',', $patterns)
        );
        $this->getAdapter()->connect();
    }

    /**
     * utility method to reinstate all triggers, disconnects adapter to ensure no locking happens
     */
    protected function enableTriggers(array $patterns = []): void
    {
        $this->getAdapter()->disconnect();
        \exec(
            'bin/robo db:triggers-import '
            . $this->getAdapter()->getOption('name') . ' '
            . \implode(',', $patterns)
        );
        $this->getAdapter()->connect();
    }
}
