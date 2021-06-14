<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Client\Event;

use ExEss\Cms\Component\Client\Adapter\ClientAdapterInterface;
use ExEss\Cms\Component\Client\Request\RequestInterface;
use ExEss\Cms\Component\Client\Response\Response;
use ExEss\Cms\Component\Client\Response\ResponseInterface;

class ResponseEvent extends AbstractEvent
{
    private Response $response;

    public function __construct(
        ClientAdapterInterface $client,
        RequestInterface $request,
        array $options,
        ResponseInterface $response
    ) {
        parent::__construct($client, $request, $options);
        $this->response = $response;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
