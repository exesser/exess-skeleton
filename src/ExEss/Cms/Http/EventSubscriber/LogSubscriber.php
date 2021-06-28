<?php declare(strict_types=1);

namespace ExEss\Cms\Http\EventSubscriber;

use ExEss\Cms\Doctrine\Type\HttpMethod;
use ExEss\Cms\Component\Flow\Request\FlowAction;
use ExEss\Cms\Helper\DataCleaner;
use ExEss\Cms\Http\Factory\PsrFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LogSubscriber implements EventSubscriberInterface
{
    private const LOG_NO_CONTENT = 'no_content';
    private const LOG_BODY = 'body';
    private const LOG_MODEL = 'model';

    public const MAX_MESSAGE_LENGTH = 10000;
    public const SUFFIX_TRUNCATED = '... (truncated)';

    private LoggerInterface $logger;

    private PsrFactory $psrFactory;

    public function __construct(
        PsrFactory $psrFactory,
        LoggerInterface $requestLogger
    ) {
        $this->logger = $requestLogger;
        $this->psrFactory = $psrFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // as last one during request event
            KernelEvents::REQUEST => ['logRequest', -100],
            // after FlashMessageSubscriber but before StreamedResponseListener
            KernelEvents::RESPONSE => ['logResponse', -1001],
        ];
    }

    public function logRequest(RequestEvent $event): void
    {
        $this->logPsrRequest($this->psrFactory->createRequest($event->getRequest()));
    }

    public function logResponse(ResponseEvent $event): void
    {
        $this->logPsrResponse(
            $this->psrFactory->createRequest($event->getRequest()),
            $this->psrFactory->createResponse($event->getResponse())
        );
    }

    public function logPsrRequest(ServerRequestInterface $request): void
    {
        $mode = $this->determineLogMode($request);

        $request->getBody()->rewind();

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

    public function logPsrResponse(ServerRequestInterface $request, ResponseInterface $response): void
    {
        $mode = $this->determineLogMode($request);

        $response->getBody()->rewind();

        $message = 'Outgoing response for: ' . $request->getUri()->getPath()
            . ', method: ' . $request->getMethod()
            . ', status: ' . $response->getStatusCode()
            . ', message: ' . $response->getReasonPhrase();
        if ($mode === self::LOG_BODY) {
            $message .= ', body: ' . $this->getTruncated($response->getBody());
        } elseif ($mode === self::LOG_MODEL) {
            $responseModel = DataCleaner::getCleanedModel(
                DataCleaner::jsonDecode($response->getBody()->getContents())['data']['model'] ?? []
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
        if ($request->getMethod() === HttpMethod::GET) {
            return self::LOG_NO_CONTENT;
        }

        // @todo check for route name instead of uri path
        // locally it starts with /, on AWS it doesn't
        $pos = \strpos($request->getUri()->getPath(), '/Api/Flow');
        if ($pos === 0 || $pos === 1) {
            return self::LOG_MODEL;
        }

        if (!\preg_match('~/login$~', $request->getUri()->getPath())) {
            return self::LOG_BODY;
        }

        return self::LOG_NO_CONTENT;
    }
}
