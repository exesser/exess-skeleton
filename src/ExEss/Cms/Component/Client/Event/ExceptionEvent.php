<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Client\Event;

use ExEss\Cms\Component\Client\Adapter\ClientAdapterInterface;
use ExEss\Cms\Component\Client\Exception\ClientRequestException;
use ExEss\Cms\Component\Client\Request\RequestInterface;

class ExceptionEvent extends AbstractEvent
{
    private ClientRequestException $exception;

    public function __construct(
        ClientAdapterInterface $client,
        RequestInterface $request,
        array $options,
        ClientRequestException $exception
    ) {
        parent::__construct($client, $request, $options);
        $this->exception = $exception;
    }

    public function getException(): ClientRequestException
    {
        return $this->exception;
    }
}
