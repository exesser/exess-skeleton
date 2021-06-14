<?php
namespace ExEss\Cms\Logger;

use Psr\Log\LoggerInterface;
use ExEss\Cms\Logger\Message\ChannelMessage;
use Symfony\Bridge\Monolog\Logger as LoggerBridge;

class Logger implements LoggerInterface
{
    public const LOGGER =
        "%%datetime%% | %%level_name%% | %%channel%% | %%extra.tag%% | %%extra.process_id%% | " .
        "[flow[%%extra.X-LOG-ID%%][%%extra.X-LOG-SPAN%%][%%extra.X-LOG-PARENT-ID%%][%%extra.X-LOG-COMPONENT%%]" .
        "[%%extra.X-LOG-USER%%]] %%message%%\n";

    // loggers
    public const CHANNEL_DEFAULT = 'app';
    public const CHANNEL_AUTHENTICATION = 'authentication';
    public const CHANNEL_REQUEST = 'request';

    public const BUSINESS = ['business' => 1];

    private LoggerBridge $logger;

    private static array $loggers = [];

    public function __construct(LoggerBridge $logger)
    {
        $this->logger =  $logger;
    }

    /**
     * Main method for handling logging a message to the logger
     *
     * @param string|array $level logging level for the message
     * @param string|array|ChannelMessage $message
     */
    public function log($level, $message, array $context = []): void
    {
        if (\is_array($message) && \count($message) === 1) {
            $message = \array_shift($message);
        }

        $channel = self::CHANNEL_DEFAULT;
        if ($message instanceof ChannelMessage) {
            $channel = $message->getChannel();
            $context = $message->getContext();
            $message = $message->getMessage();
        } elseif (!empty($context['channel'])) {
            $channel = $context['channel'];
        }

        if (\is_array($message)) {
            $message = \var_export($message, true);
        }

        $this->getLogger($channel)->$level($message, $context);
    }

    public function getLogger(string $channel): LoggerBridge
    {
        if (isset(self::$loggers[$channel])) {
            $logger = self::$loggers[$channel];
        } else {
            $logger = self::$loggers[$channel] = $this->logger->withName($channel);
        }

        return $logger;
    }

    /**
     * @param string|array|ChannelMessage $message
     */
    public function emergency($message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }

    /**
     * @param string|array|ChannelMessage $message
     */
    public function alert($message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }

    /**
     * @param string|array|ChannelMessage $message
     */
    public function critical($message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }

    /**
     * @param string|array|ChannelMessage $message
     */
    public function error($message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    /**
     * @param string|array|ChannelMessage $message
     */
    public function warning($message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    /**
     * @param string|array|ChannelMessage $message
     */
    public function notice($message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }

    /**
     * @param string|array|ChannelMessage $message
     */
    public function info($message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    /**
     * @param string|array|ChannelMessage $message
     */
    public function debug($message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }
}
