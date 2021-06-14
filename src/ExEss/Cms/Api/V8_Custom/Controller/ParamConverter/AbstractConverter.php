<?php

namespace ExEss\Cms\Api\V8_Custom\Controller\ParamConverter;

use Slim\Http\Request;

abstract class AbstractConverter
{
    protected function getRequestParameters(Request $request): array
    {
        $routeArgs = \array_map(
            function ($value) {
                return \is_bool($value)? $value : \urldecode($value);
            },
            $request->getAttribute('route')->getArguments()
        );

        return \array_merge(
            $routeArgs,
            $request->getQueryParams() ?? [],
            $request->getParsedBody() ?? []
        );
    }
}
