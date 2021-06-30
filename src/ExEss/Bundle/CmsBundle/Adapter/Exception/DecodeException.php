<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Adapter\Exception;

class DecodeException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 0);
    }
}
