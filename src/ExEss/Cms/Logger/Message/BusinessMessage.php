<?php
namespace ExEss\Cms\Logger\Message;

use ExEss\Cms\Logger\Logger;

class BusinessMessage extends ChannelMessage
{
    /**
     * @param array $context
     */
    public function __construct(string $message, array $context = [])
    {
        parent::__construct($message, \array_merge(Logger::BUSINESS, $context));
    }
}
