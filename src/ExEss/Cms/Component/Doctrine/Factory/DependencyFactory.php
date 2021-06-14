<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Factory;

use Doctrine\Migrations\Configuration\EntityManager\EntityManagerLoader;
use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Doctrine\Migrations\DependencyFactory as MigrationsDependencyFactory;
use Doctrine\Migrations\Generator\DiffGenerator;
use ExEss\Cms\Component\Doctrine\Provider\SchemaProvider;
use ExEss\Cms\Component\Doctrine\Schema\SchemaManager;
use Psr\Log\LoggerInterface;

class DependencyFactory
{
    public static function fromEntityManager(
        ConfigurationLoader $configurationLoader,
        EntityManagerLoader $emLoader,
        SchemaProvider $schemaProvider,
        ?LoggerInterface $logger = null
    ): MigrationsDependencyFactory {
        $dependencyFactory = MigrationsDependencyFactory::fromEntityManager($configurationLoader, $emLoader, $logger);
        $dependencyFactory->setDefinition(
            DiffGenerator::class,
            static function () use ($schemaProvider, $dependencyFactory): DiffGenerator {
                return new DiffGenerator(
                    $dependencyFactory->getConnection()->getConfiguration(),
                    new SchemaManager($dependencyFactory->getConnection()->getSchemaManager()),
                    $schemaProvider,
                    $dependencyFactory->getConnection()->getDatabasePlatform(),
                    $dependencyFactory->getMigrationGenerator(),
                    $dependencyFactory->getMigrationSqlGenerator(),
                    $schemaProvider
                );
            }
        );

        return $dependencyFactory;
    }
}
