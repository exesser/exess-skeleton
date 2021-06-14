<?php
namespace ExEss\Cms\Api\Core;

use ExEss\Cms\App;
use ExEss\Cms\Doctrine\Type\HttpMethod;
use ExEss\Cms\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Users\Security\Route\Voter\RouteVoter;

class RouteLoader
{
    public static function configureRoutes(App $app): void
    {
        $app->options(
            RouteVoter::OPTIONS_PATTERN,
            function (Request $request, Response $response, $args): Response {
                return $response;
            }
        );

        require __DIR__ . "/../../../../../config/routes-slim.php";

        // Catch-all route to serve a 404 Not Found page if none of the routes match
        // NOTE: make sure this route is defined last
        $app->map(
            \array_keys(HttpMethod::getValues()),
            '/{routes:.+}',
            function (): void {
                throw new NotFoundException('Route not found');
            }
        );
    }
}
