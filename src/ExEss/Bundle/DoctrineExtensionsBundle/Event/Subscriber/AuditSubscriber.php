<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle\Event\Subscriber;

use Doctrine\Common\Annotations\Reader;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\JoinTable;
use ExEss\Bundle\DoctrineExtensionsBundle\Mapping\Annotation\Auditable;
use ExEss\Bundle\DoctrineExtensionsBundle\Schema\Schema;
use ExEss\Bundle\DoctrineExtensionsBundle\Schema\Table as HelperTable;
use ExEss\Bundle\DoctrineExtensionsBundle\Type\AuditOperationEnumType;
use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractEnumType;
use ExEss\Bundle\DoctrineExtensionsBundle\Event\Event\AfterSchemaGenerationEntityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuditSubscriber implements EventSubscriberInterface
{
    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            AfterSchemaGenerationEntityEvent::NAME => 'addAuditing'
        ];
    }

    public function addAuditing(AfterSchemaGenerationEntityEvent $event): void
    {
        if ($this->isAuditable($event->getClassMetadata()->getReflectionClass())) {
            $this->setupAuditing($event->getSchema(), $event->getTable());
        }

        foreach ($event->getClassMetadata()->getReflectionProperties() as $key => $property) {
            if ($this->reader->getPropertyAnnotation($property, Auditable::class) instanceof Auditable
                && ($this->reader->getPropertyAnnotation($property, JoinTable::class)) instanceof JoinTable
            ) {
                $table = $event->getClassMetadata()->getAssociationMapping($key)['joinTable']['name'];
                if ($event->getSchema()->hasTable($table)) {
                    $this->setupAuditing($event->getSchema(), $event->getSchema()->getTable($table));
                }
            }
        }
    }

    private function isAuditable(\ReflectionClass $reflectionClass): bool
    {
        $auditable = false;
        if ($reflectionClass->getParentClass()) {
            $auditable = $this->isAuditable($reflectionClass->getParentClass());
        }

        return $auditable || $this->reader->getClassAnnotation(
            $reflectionClass,
            Auditable::class
        ) instanceof Auditable;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException When the tablename or operation for creating a trigger is incorrect
     */
    private function setupAuditing(Schema $schema, Table $table): void
    {
        $auditTableName = self::getAuditTableName($table->getName());
        $auditTable = $schema->createTable($auditTableName);
        $auditTable->addColumn('audit_timestamp', 'datetime_immutable_microseconds');
        $auditTable->addColumn('audit_operation', 'enum_audit_operation');

        foreach ((new HelperTable($table))->getColumns() as $column) {
            $auditTable->addColumn($column->getName(), $this->getType($column)->getName(), \array_merge(
                $column->toArray(),
                [
                    'default' => null,
                    'notnull' => false,
                    'autoincrement' => false,
                    'comment' => '',
                    'type' => $this->getType($column),
                ]
            ));
        }

        $auditTable->setPrimaryKey(
            \array_merge(['audit_timestamp'], $table->getPrimaryKeyColumns(), ['audit_operation'])
        );

        $auditTable->addIndex(
            \array_merge(['audit_operation'], $table->getPrimaryKeyColumns()),
            'idx_operation_id'
        );

        foreach (AuditOperationEnumType::getValues() as $operation) {
            $schema->createTrigger(
                $operation,
                $operation === AuditOperationEnumType::DELETE ? 'BEFORE' : 'AFTER',
                $table,
                $auditTableName
            );
        }
    }

    private function getType(Column $column): Type
    {
        if ($column->getType() instanceof AbstractEnumType) {
            return Type::getType('string');
        }

        return $column->getType();
    }

    public static function getAuditTableName(string $tableName): string
    {
        return $tableName . '_aud';
    }
}
