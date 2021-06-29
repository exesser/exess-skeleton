<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SoapServicesPass implements CompilerPassInterface
{
    public const SOAP_PROXY_SERVICES = 'soap_proxies_to_generate';

    public function process(ContainerBuilder $container): void
    {
        $configParameters = [];
        foreach ($container->findTaggedServiceIds('client.soap') as $service => $attributes) {
            $configParameters[$service] = \trim($container->getDefinition($service)->getArgument(0), '%');
        }

        $container->setParameter(
            self::SOAP_PROXY_SERVICES,
            $configParameters
        );
    }
}
