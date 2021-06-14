<?php declare(strict_types=1);

namespace ExEss\Cms\Adapter\Response;

interface Response
{
    public function getStatusCode(): int;
    public function getBody(): ?array;
    public function __toString(): ?string;
}
