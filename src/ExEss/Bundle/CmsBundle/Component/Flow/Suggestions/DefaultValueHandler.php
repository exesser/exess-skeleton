<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Suggestions;

use ExEss\Bundle\CmsBundle\Entity\Flow;
use ExEss\Bundle\CmsBundle\Component\Flow\DefaultValueService;
use ExEss\Bundle\CmsBundle\Component\Flow\Request\FlowAction;
use ExEss\Bundle\CmsBundle\Component\Flow\Response;

class DefaultValueHandler extends AbstractSuggestionHandler
{
    protected DefaultValueService $service;

    public function __construct(DefaultValueService $service)
    {
        $this->service = $service;
    }

    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool
    {
        return $response->getForm() !== null
            && \count($response->getForm()->getGroups())
        ;
    }

    protected function doHandle(Response $response, FlowAction $action, Flow $flow): void
    {
        $this->service->resolveDefaults(
            $response->getModel(),
            $response->getForm()->getGroups()
        );
    }
}
