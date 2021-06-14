<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Client\Response;

interface ResponseInterface
{
    public function getStatusCode(): int;

    public function getHeaders(): array;

    /**
     * @return string|\stdClass
     */
    public function getData();
}
