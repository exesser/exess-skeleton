<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Schema;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use ExEss\Cms\Component\Doctrine\Event\Subscriber\AuditSubscriber;
use ExEss\Cms\Component\Doctrine\Type\AbstractEnumType;

/**
 * Not added to container
 */
final class SchemaManager extends AbstractSchemaManager
{
    private AbstractSchemaManager $schemaManager;

    public function __construct(AbstractSchemaManager $schemaManager)
    {
        parent::__construct($schemaManager->_conn, $schemaManager->_platform);
        $this->schemaManager = $schemaManager;
    }

    /**
     * @inheritDoc
     */
    protected function _getPortableTableColumnDefinition($tableColumn): Column // @codingStandardsIgnoreLine
    {
        return $this->schemaManager->_getPortableTableColumnDefinition($tableColumn);
    }

    public function createSchema(): Schema
    {
        $schema = new Schema($this->schemaManager->createSchema());

        // Fetch triggers
        $triggers = $this->_conn->fetchAll('show triggers;');
        foreach ($triggers as $trigger) {
            $schema->createTrigger(
                $trigger['Event'],
                $trigger['Timing'],
                $schema->getTable($trigger['Table']),
                AuditSubscriber::getAuditTableName($trigger['Table']),
                $trigger['Statement']
            );
        }

        foreach ($schema->getTables() as $table) {
            if (\substr($table->getName(), 0, 5) === AbstractEnumType::PREFIX) {
                $values = $this->_conn->fetchAll('select id from ' . $table->getName());
                if (!empty($values)) {
                    $schema->addInsert(new EnumInsert($table, \array_map(function (array $value) {
                        return $value['id'];
                    }, $values)));
                }
            }
        }

        return $schema;
    }
}
