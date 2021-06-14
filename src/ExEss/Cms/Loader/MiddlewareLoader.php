<?php declare(strict_types=1);

namespace ExEss\Cms\Loader;

use ExEss\Cms\App;

/**
 * Class to load default middleware and to add custom application middleware.
 */
class MiddlewareLoader
{
    /**
     * @var iterable
     */
    private iterable $middleware;

    public function __construct(iterable $middleware)
    {
        $this->middleware = $middleware;
    }

    public function configureMiddleware(App $app): void
    {
        foreach ($this->middleware as $middleware) {
            $app->add($middleware);
        }
    }
}
