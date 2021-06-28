<?php
namespace ExEss\Cms\Component\Flow\Suggestions;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Component\Flow\Request\FlowAction;
use ExEss\Cms\Component\Flow\Response;

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
