<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool as BaseSchemaTool;
use ExEss\Cms\Component\Doctrine\Event\Event\AfterSchemaGenerationColumnEvent;
use ExEss\Cms\Component\Doctrine\Schema\Schema;
use ExEss\Cms\Component\Doctrine\Event\Event\AfterSchemaGenerationEntityEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Auto-wired
 */
final class SchemaTool extends BaseSchemaTool
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($entityManager);
        $this->dispatcher = $dispatcher;
    }

    public function getSchemaFromMetadata(array $classes): Schema
    {
        $baseSchema = parent::getSchemaFromMetadata($classes);
        $schema = new Schema($baseSchema);

        /** @var \Doctrine\ORM\Mapping\ClassMetadata $classMetadata */
        foreach ($classes as $classMetadata) {
            if (!$schema->hasTable($classMetadata->getTableName())) {
                continue;
            }

            $table = $schema->getTable($classMetadata->getTableName());
            $this->dispatcher->dispatch(new AfterSchemaGenerationEntityEvent(
                $schema,
                $table,
                $classMetadata
            ), AfterSchemaGenerationEntityEvent::NAME);

            foreach ($table->getColumns() as $column) {
                $this->dispatcher->dispatch(new AfterSchemaGenerationColumnEvent(
                    $schema,
                    $table,
                    $column
                ), AfterSchemaGenerationColumnEvent::NAME);
            }
        }

        return $schema;
    }
}
