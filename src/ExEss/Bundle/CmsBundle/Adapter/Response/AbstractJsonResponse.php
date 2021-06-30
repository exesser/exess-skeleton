<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Adapter\Response;

abstract class AbstractJsonResponse extends AbstractResponse
{
    public function __construct()
    {
        parent::__construct(0, null);
    }

    public function __toString(): string
    {
        return 'Not used in json response';
    }
}
