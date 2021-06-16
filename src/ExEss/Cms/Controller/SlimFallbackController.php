<?php declare(strict_types=1);

namespace ExEss\Cms\Controller;

use ExEss\Cms\App;
use ExEss\Cms\Cleaner\RequestCleaner;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SlimFallbackController
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * @Route("/{anything}", requirements={ "anything"=".+" }, priority="-100")
     * @Route("/", priority="-100")
     */
    public function __invoke(Request $request): Response
    {
        (new RequestCleaner())();
        $response = $this->app->runWithRequest($request);

        return (new HttpFoundationFactory())->createResponse($response);
    }
}
