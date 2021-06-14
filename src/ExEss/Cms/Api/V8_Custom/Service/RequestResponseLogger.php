<?php
namespace ExEss\Cms\Api\V8_Custom\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use ExEss\Cms\FLW_Flows\Request\FlowAction;

class RequestResponseLogger
{
    private const LOG_NO_CONTENT = 'no_content';
    private const LOG_BODY = 'body';
    private const LOG_MODEL = 'model';

    public const MAX_MESSAGE_LENGTH = 10000;
    public const SUFFIX_TRUNCATED = '... (truncated)';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $requestLogger)
    {
        $this->logger = $requestLogger;
    }

    public function logRequest(ServerRequestInterface $request): void
    {
        $mode = $this->determineLogMode($request);

        $message = 'Incoming call for: ' . $request->getUri()->getPath();
        $message .= ', method: ' . $request->getMethod();
        if ($mode === self::LOG_BODY) {
            $message .= ', body: ' . $this->getTruncated($request->getBody());
        } elseif ($mode === self::LOG_MODEL) {
            $requestModel =  DataCleaner::getCleanedModel($request->getParsedBody()['model'] ?? []);
            $action = ((array) $request->getParsedBody())['action'] ?? [];
            $requestEvent = ((array) $action)['event'] ?? FlowAction::EVENT_INIT;
            $message .= ', event: ' . \json_encode($requestEvent) . ', model: ' . \json_encode($requestModel);
        }

        $this->logger->info($message);
    }

    public function logResponse(ServerRequestInterface $request, ResponseInterface $response): void
    {
        $mode = $this->determineLogMode($request);

        $message = 'Outgoing response for: ' . $request->getUri()->getPath()
            . ', method: ' . $request->getMethod()
            . ', status: ' . $response->getStatusCode()
            . ', message: ' . $response->getReasonPhrase();
        if ($mode === self::LOG_BODY) {
            $message .= ', body: ' . $this->getTruncated($response->getBody());
        } elseif ($mode === self::LOG_MODEL) {
            $responseModel = DataCleaner::getCleanedModel(
                \json_decode($response->getBody(), true)['data']['model'] ?? []
            );
            $message .= ', model: ' . \json_encode($responseModel);
        }

        $this->logger->info($message);
    }

    private function getTruncated(StreamInterface $stream): string
    {
        return $stream->getSize() > self::MAX_MESSAGE_LENGTH ?
            $stream->read(self::MAX_MESSAGE_LENGTH) .  self::SUFFIX_TRUNCATED :
            (string) $stream
        ;
    }

    private function determineLogMode(ServerRequestInterface $request): string
    {
        if ($request->getMethod() === 'GET') {
            return self::LOG_NO_CONTENT;
        }

        // locally it starts with /, on AWS it doesn't
        $pos = \strpos($request->getUri()->getPath(), '/Api/V8_Custom/Flow');
        if ($pos === 0 || $pos === 1) {
            return self::LOG_MODEL;
        }

        if (!\preg_match('~/login$~', $request->getUri()->getPath())) {
            return self::LOG_BODY;
        }

        return self::LOG_NO_CONTENT;
    }
}
