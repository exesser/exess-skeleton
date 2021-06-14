<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Provider;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\Exception\NoMappingFound;
use Doctrine\Migrations\Provider\SchemaProvider as ProviderSchemaProvider;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Auto-wired
 */
final class SchemaProvider implements ProviderSchemaProvider
{
    private EntityManagerInterface $entityManager;

    private SchemaTool $schemaTool;

    public function __construct(EntityManagerInterface $em, SchemaTool $schemaTool)
    {
        $this->entityManager = $em;
        $this->schemaTool = $schemaTool;
    }

    /**
     * @throws NoMappingFound
     */
    public function createSchema(): Schema
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        if (\count($metadata) === 0) {
            throw NoMappingFound::new();
        }

        return $this->schemaTool->getSchemaFromMetadata($metadata);
    }
}
