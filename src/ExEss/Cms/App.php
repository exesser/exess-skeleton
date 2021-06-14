<?php declare(strict_types=1);

namespace ExEss\Cms;

use ExEss\Cms\Api\Core\RouteLoader;
use ExEss\Cms\Loader\MiddlewareLoader;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Slim\Exception\InvalidMethodException;
use Slim\Http\Headers;
use Slim\Http\Request as HttpRequest;
use Slim\Http\Stream;
use Slim\Http\Uri;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;

class App extends \Slim\App
{
    private MiddlewareLoader $middlewareLoader;

    public function __construct(ContainerInterface $container, MiddlewareLoader $middlewareLoader)
    {
        parent::__construct($container);
        $this->middlewareLoader = $middlewareLoader;
        RouteLoader::configureRoutes($this);
    }

    /**
     * @inheritDoc
     */
    public function runWithRequest(Request $request)
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($request);

        $this->middlewareLoader->configureMiddleware($this);

        $response = $this->getContainer()->get('response');

        try {
            $uri = $psrRequest->getUri();
            $userInfo = \explode(':', $uri->getUserInfo());

            $stream = \fopen('php://memory', 'r+');
            \fwrite($stream, $psrRequest->getBody()->getContents());
            \rewind($stream);

            $response = $this->process(
                new HttpRequest(
                    $psrRequest->getMethod(),
                    new Uri(
                        $uri->getScheme(),
                        $uri->getHost(),
                        $uri->getPort(),
                        $uri->getPath(),
                        $uri->getQuery(),
                        $uri->getFragment(),
                        $userInfo[0] ?? '',
                        $userInfo[1] ?? ''
                    ),
                    new Headers($psrRequest->getHeaders()),
                    $psrRequest->getCookieParams(),
                    $psrRequest->getServerParams(),
                    new Stream($stream),
                    $psrRequest->getUploadedFiles()
                ),
                $response
            );
        } catch (InvalidMethodException $e) {
            $response = $this->processInvalidMethod($e->getRequest(), $response);
        }

        return $response;
    }

    /**
     * Provides the ability to reset the middleware stack.
     * Needed because our middleware is not stateless (example: uses/registers cookies), and since this
     * middleware are loaded on the Slim app they are "stored" on the App and a restoreContainer on the
     * DI container has not the effect we would expect (since an instance of these services remains
     * loaded on the application)
     */
    public function reset(): void
    {
        $this->stack = null;
        $this->middlewareLock = false;
    }
}
