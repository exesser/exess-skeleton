<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\DependencyInjection\Compiler;

use ExEss\Bundle\CmsBundle\Component\Client\Adapter\GuzzleClientAdapter;
use ExEss\Bundle\CmsBundle\Component\Client\Client;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GuzzleClientPass implements CompilerPassInterface
{
    private function getMappedClients(): array
    {
        // @todo fetch the relevant services from the container
        return [];
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($this->getMappedClients() as $clientKey => $clientParamsKey) {
            $guzzleClient = (new Definition(\GuzzleHttp\Client::class))
                ->setFactory([\ExEss\Bundle\CmsBundle\Factory\GuzzleClientFactory::class, 'create'])
                ->setArguments(['%' . $clientParamsKey . '%'])
                ->setPublic(true);

            $container->setDefinition($guzzleClientKey = $clientKey . 'GuzzleClient', $guzzleClient);

            $clientAdapter = (new Definition(GuzzleClientAdapter::class))->setArguments([
                new Reference($guzzleClientKey),
                new Reference(\Symfony\Component\Serializer\Serializer::class),
            ]);

            $client = (new Definition(Client::class))
                ->setPublic(true)
                ->setArguments([
                    $clientAdapter,
                    new Reference(EventDispatcherInterface::class),
                ]);

            $container->setDefinition($clientKey . 'Client', $client);
        }
    }
}
