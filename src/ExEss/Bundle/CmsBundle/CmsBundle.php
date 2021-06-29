<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle;

use ExEss\Bundle\CmsBundle\DependencyInjection\CmsExtension;
use ExEss\Bundle\CmsBundle\DependencyInjection\Compiler\CachePass;
use ExEss\Bundle\CmsBundle\DependencyInjection\Compiler\GuzzleClientPass;
use ExEss\Bundle\CmsBundle\DependencyInjection\Compiler\SoapServicesClientPass;
use ExEss\Bundle\CmsBundle\DependencyInjection\Compiler\SoapServicesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CmsBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CachePass());
        $container->addCompilerPass(new SoapServicesPass());
        $container->addCompilerPass(new SoapServicesClientPass());
        $container->addCompilerPass(new GuzzleClientPass());
    }

    public function getContainerExtension(): CmsExtension
    {
        return new CmsExtension();
    }
}
