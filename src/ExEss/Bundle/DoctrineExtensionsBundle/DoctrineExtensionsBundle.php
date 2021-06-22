<?php declare(strict_types=1);

namespace ExEss\Bundle\DoctrineExtensionsBundle;

use ExEss\Bundle\DoctrineExtensionsBundle\DependencyInjection\BundleExtension;
use ExEss\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler\MigrationsDiffPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoctrineExtensionsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new MigrationsDiffPass());
    }

    public function getContainerExtension(): BundleExtension
    {
        return new BundleExtension();
    }
}
