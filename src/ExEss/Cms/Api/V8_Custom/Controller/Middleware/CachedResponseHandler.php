<?php

namespace ExEss\Cms\Api\V8_Custom\Controller\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\V8_Custom\Service\Security;
use Symfony\Component\Cache\Adapter\AdapterInterface as Cache;

class CachedResponseHandler
{
    private Cache $cache;

    private Security $security;

    public function __construct(Cache $cache, Security $security)
    {
        $this->cache = $cache;
        $this->security = $security;
    }

    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $userId = $this->security->getCurrentUserId();

        // never cache if not authenticated
        if (!$userId) {
            return $next($request, $response);
        }

        $routeArgs = \array_map(
            function ($value) {
                return \is_bool($value)? $value : \urldecode($value);
            },
            $request->getAttribute('route')->getArguments()
        );

        $requestHash = \base64_encode(\json_encode(\array_merge(
            [
                'user' => $userId,
                'url' => $request->getUri(),
                'query' => $request->getQueryParams(),
            ],
            $routeArgs,
            $request->getQueryParams() ?? []
        )));

        $cacheItem = $this->cache->getItem($requestHash);
        if ($cacheItem->isHit()) {
            $response->getBody()->write($cacheItem->get());
            return $response;
        }

        /** @var Response $response */
        $response = $next($request, $response);
        $response->getBody()->rewind();
        $cacheItem->set($response->getBody()->getContents());
        $response->getBody()->rewind();
        $this->cache->save($cacheItem);

        return $response;
    }
}
