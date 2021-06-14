<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Params\LogParams;
use ExEss\Cms\Logger\Logger;

class LogController extends AbstractApiController
{
    private Logger $logger;

    public function __construct(
        Logger $logger
    ) {
        $this->logger = $logger;
    }

    public function error(Request $request, Response $response, array $args, LogParams $params): Response
    {
        $errorData = [
            'name' => $params->getName(),
            'state' => $params->getState(),
            'url' => $params->getUrl(),
        ];

        if ($params->getStack()) {
            $stack = \explode("\n", $params->getStack());
            $stack = \array_map('trim', $stack);
            $errorData['stack'] = $stack;
        }

        if ($params->getCause()) {
            $errorData['cause'] = $params->getCause();
        }

        $this->logger->error(\json_encode($errorData, \JSON_PRETTY_PRINT));

        return $this->generateResponse($response, 200, 'OK');
    }
}
