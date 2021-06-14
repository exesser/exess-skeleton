<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Client;

use ExEss\Cms\Component\Client\Adapter\ClientAdapterInterface;
use ExEss\Cms\Component\Client\Event\ExceptionEvent;
use ExEss\Cms\Component\Client\Event\RequestEvent;
use ExEss\Cms\Component\Client\Event\ResponseEvent;
use ExEss\Cms\Component\Client\Exception\ClientRequestException;
use ExEss\Cms\Component\Client\Request\RequestInterface;
use ExEss\Cms\Component\Client\Response\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Client implements ClientAdapterInterface
{
    private ClientAdapterInterface $client;

    private EventDispatcher $dispatcher;

    public function __construct(ClientAdapterInterface $client, EventDispatcherInterface $dispatcher)
    {
        $this->client = $client;
        $this->dispatcher = $dispatcher;
    }

    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        $event = new RequestEvent($this->client, $request, $options);
        $this->dispatcher->dispatch($event);

        try {
            $response = $this->client->send($request, $event->getOptions());
        } catch (ClientRequestException $e) {
            $this->dispatcher->dispatch(new ExceptionEvent($this->client, $request, $options, $e));

            throw $e;
        }

        $this->dispatcher->dispatch(new ResponseEvent($this->client, $request, $options, $response));

        return $response;
    }

    public function getClientConfig(): ClientConfig
    {
        return $this->client->getClientConfig();
    }

    /**
     * @inheritDoc
     */
    public function getClient()
    {
        return $this->client->getClient();
    }
}
