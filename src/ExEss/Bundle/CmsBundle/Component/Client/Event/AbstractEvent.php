<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Client\Event;

use ExEss\Bundle\CmsBundle\Component\Client\Adapter\ClientAdapterInterface;
use ExEss\Bundle\CmsBundle\Component\Client\Request\RequestInterface;
use Symfony\Contracts\EventDispatcher\Event as SymfonyEvent;

abstract class AbstractEvent extends SymfonyEvent
{
    private ClientAdapterInterface $client;

    private RequestInterface $request;

    protected array $options;

    public function __construct(ClientAdapterInterface $client, RequestInterface $request, array $options)
    {
        $this->client = $client;
        $this->request = $request;
        $this->options = $options;
    }

    public function getClient(): ClientAdapterInterface
    {
        return $this->client;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
