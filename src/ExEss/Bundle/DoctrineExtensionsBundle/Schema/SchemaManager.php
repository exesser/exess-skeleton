<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle\Schema;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use ExEss\Bundle\DoctrineExtensionsBundle\Event\Subscriber\AuditSubscriber;
use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;

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
        $triggers = $this->_conn->fetchAllAssociative('show triggers;');
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
                $values = $this->_conn->fetchAllAssociative('select id from ' . $table->getName());
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
