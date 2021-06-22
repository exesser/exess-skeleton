<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MigrationsDiffPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition('doctrine_migrations.diff_command')->replaceArgument(
            0,
            new Reference('ExEss\Bundle\DoctrineExtensionsBundle\Factory\DependencyFactory')
        );

        $container->getDefinition('doctrine_migrations.dump_schema_command')->replaceArgument(
            0,
            new Reference('ExEss\Bundle\DoctrineExtensionsBundle\Factory\DependencyFactory')
        );
    }
}
