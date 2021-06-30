<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Client\Event;

use ExEss\Bundle\CmsBundle\Component\Client\Adapter\ClientAdapterInterface;
use ExEss\Bundle\CmsBundle\Component\Client\Request\RequestInterface;
use ExEss\Bundle\CmsBundle\Component\Client\Response\Response;
use ExEss\Bundle\CmsBundle\Component\Client\Response\ResponseInterface;

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
