<?php
namespace ExEss\Cms\FLW_Flows\Suggestions;

use ExEss\Cms\Entity\Flow;
use ExEss\Cms\FLW_Flows\Request\FlowAction;
use ExEss\Cms\FLW_Flows\Response;

interface SuggestionHandler
{
    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool;

    public function handleModel(Response $response, FlowAction $action, Flow $flow): void;
}
