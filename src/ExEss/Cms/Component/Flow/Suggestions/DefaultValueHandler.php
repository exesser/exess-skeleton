<?php
namespace ExEss\Cms\Component\Flow\Suggestions;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Component\Flow\DefaultValueService;
use ExEss\Cms\Component\Flow\Request\FlowAction;
use ExEss\Cms\Component\Flow\Response;

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
