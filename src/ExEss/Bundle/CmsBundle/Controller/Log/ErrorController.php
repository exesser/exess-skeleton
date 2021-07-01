<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\Log;

use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
use ExEss\Bundle\CmsBundle\Logger\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/log/error")
     * @ParamConverter("jsonBody")
     */
    public function __invoke(Body\ErrorBody $jsonBody): SuccessResponse
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
