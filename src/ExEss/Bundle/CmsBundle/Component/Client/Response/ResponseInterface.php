<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Component\Client\Response;

interface ResponseInterface
{
    public function getStatusCode(): int;

    public function getHeaders(): array;

    /**
     * @return string|\stdClass
     */
    public function getData();
}
