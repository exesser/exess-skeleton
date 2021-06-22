<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle\Event\Subscriber;

use ExEss\Bundle\DoctrineExtensionsBundle\Event\Event\AfterSchemaGenerationColumnEvent;
use ExEss\Bundle\DoctrineExtensionsBundle\Schema\EnumInsert;
use ExEss\Bundle\DoctrineExtensionsBundle\Type\AbstractLargeEnumType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LargeEnumTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            AfterSchemaGenerationColumnEvent::NAME => 'addEnums'
        ];
    }

    public function addEnums(AfterSchemaGenerationColumnEvent $event): void
    {
        if (!$event->getColumn()->getType() instanceof AbstractLargeEnumType) {
            return;
        }

        $enumTable = $event->getSchema()->createTable($event->getColumn()->getType()->getName());
        $enumTable->addColumn('id', 'string');
        $enumTable->setPrimaryKey(['id']);

        $event->getTable()->addForeignKeyConstraint($enumTable->getName(), [$event->getColumn()->getName()], ['id']);

        $event->getSchema()->addInsert(
            new EnumInsert($enumTable, \array_keys($event->getColumn()->getType()::getValues()))
        );
    }
}
