<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Event\Subscriber;

use ExEss\Cms\Component\Doctrine\Event\Event\AfterSchemaGenerationColumnEvent;
use ExEss\Cms\Component\Doctrine\Schema\EnumInsert;
use ExEss\Cms\Component\Doctrine\Type\AbstractLargeEnumType;
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
