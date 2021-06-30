<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Suggestions;

use ExEss\Bundle\CmsBundle\Entity\Flow;
use ExEss\Bundle\CmsBundle\Component\Flow\Request\FlowAction;
use ExEss\Bundle\CmsBundle\Component\Flow\Response;

abstract class AbstractSuggestionHandler implements SuggestionHandler
{
    final public function handleModel(Response $response, FlowAction $action, Flow $flow): void
    {
        if (!static::shouldHandle($response, $action, $flow)) {
            return;
        }
        $this->doHandle($response, $action, $flow);
    }

    abstract public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool;

    abstract protected function doHandle(Response $response, FlowAction $action, Flow $flow): void;
}
