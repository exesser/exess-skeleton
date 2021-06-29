<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\DependencyInjection\Compiler;

use ExEss\Cms\Component\Client\Adapter\SoapServiceAdapter;
use ExEss\Cms\Component\Client\Client;
use ExEss\Cms\ExternalAPI\AbstractSoapClientService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SoapServicesClientPass implements CompilerPassInterface
{
    private function getMappedClients(ContainerBuilder $container): array
    {
        // @todo fetch the relevant services from the container
        return [];
    }

    public function process(ContainerBuilder $container): void
    {
        foreach ($this->getMappedClients($container) as $clientKey => $clientInfo) {
            $params = $container->getParameter($clientInfo['clientParams']);
            $soapParams = (new Definition())
                ->setFactory([AbstractSoapClientService::class, 'getClientOptions'])
                ->setArguments(
                    ['%' . $clientInfo['clientParams'] . '%']
                );

            $soapClient = (new Definition($clientInfo['class']))->setArguments([$soapParams]);

            foreach ($clientInfo['addMethodCall'] ?? [] as $call) {
                $soapClient->addMethodCall($call['method'], $call['arguments']);
            }

            $clientAdapter = (new Definition(SoapServiceAdapter::class))->setArguments([
                $soapClient,
                $params['wsdl'],
                new Reference('monolog.logger.client-request'),
            ]);

            $client = (new Definition(Client::class))
                ->setArguments([
                    $clientAdapter,
                    new Reference(EventDispatcherInterface::class),
                ])->setLazy(true);

            $container->setDefinition($clientKey . 'Client', $client);
        }
    }
}
