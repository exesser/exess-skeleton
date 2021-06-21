<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Log;

use ExEss\Cms\Controller\Log\Body\ErrorBody;
use ExEss\Cms\Http\SuccessResponse;
use ExEss\Cms\Logger\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/Api/log/error")
     * @ParamConverter("jsonBody")
     */
    public function __invoke(Request $request, ErrorBody $jsonBody): SuccessResponse
    {
        $errorData = [
            'name' => $jsonBody->getName(),
            'state' => $jsonBody->getState(),
            'url' => $jsonBody->getUrl(),
        ];

        if ($stack = $jsonBody->getStack()) {
            $stack = \explode("\n", $stack);
            $stack = \array_map('trim', $stack);
            $errorData['stack'] = $stack;
        }

        if ($cause = $jsonBody->getCause()) {
            $errorData['cause'] = $cause;
        }

        $this->logger->error(\json_encode($errorData, \JSON_PRETTY_PRINT));

        return new SuccessResponse();
    }
}