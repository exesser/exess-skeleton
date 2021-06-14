<?php
namespace ExEss\Cms\Logger\Message;

use ExEss\Cms\Logger\Logger;

class ChannelMessage
{
    private string $channel = Logger::CHANNEL_DEFAULT;

    private string $message;

    private array $context;

    public function __construct(string $message, array $context = [])
    {
        $this->message = $message;
        $this->context = $context;
    }

    /**
     * @param mixed $message
     */
    public static function byChannel(
        $message,
        string $channel = Logger::CHANNEL_DEFAULT,
        array $context = []
    ): ChannelMessage {

        $channelMessage = new self($message, $context);
        $channelMessage->channel = $channel;

        return $channelMessage;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }
}
