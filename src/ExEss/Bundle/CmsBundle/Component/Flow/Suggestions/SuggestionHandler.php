<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Suggestions;

use ExEss\Bundle\CmsBundle\Entity\Flow;
use ExEss\Bundle\CmsBundle\Component\Flow\Request\FlowAction;
use ExEss\Bundle\CmsBundle\Component\Flow\Response;

interface SuggestionHandler
{
    public static function shouldHandle(Response $response, FlowAction $action, Flow $flow): bool;

    public function handleModel(Response $response, FlowAction $action, Flow $flow): void;
}
