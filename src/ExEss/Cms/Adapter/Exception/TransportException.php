<?php declare(strict_types=1);

namespace ExEss\Cms\Adapter\Exception;

class TransportException extends \Exception
{
    public function __construct(string $message, ?\Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
