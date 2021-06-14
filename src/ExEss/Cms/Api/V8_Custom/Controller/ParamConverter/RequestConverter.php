<?php

namespace ExEss\Cms\Api\V8_Custom\Controller\ParamConverter;

use Slim\Http\Request;
use Slim\Http\Response;

class RequestConverter extends AbstractConverter
{
    public const ATTRIBUTE = 'converted_parameters';

    private array $mapping;

    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $arguments = $this->getRequestParameters($request);

        $argumentsChanged = false;
        foreach ($arguments as $key => $value) {
            if (\array_key_exists($key, $this->mapping)) {
                $arguments[$this->mapping[$key]] = $value;
                $argumentsChanged = true;
            }
        }

        if ($argumentsChanged) {
            $request = $request->withQueryParams($arguments);
            $request = $request->withAttribute(self::ATTRIBUTE, \array_keys($this->mapping));
        }

        return $next($request, $response);
    }
}
