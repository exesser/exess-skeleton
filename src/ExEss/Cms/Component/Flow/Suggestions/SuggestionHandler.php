<?php
namespace ExEss\Cms\Component\Flow\Suggestions;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\Component\Flow\Request\FlowAction;
use ExEss\Cms\Component\Flow\Response;

interface SuggestionHandler
{
    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool;

    public function handleModel(Response $response, FlowAction $action, Flow $flow): void;
}
