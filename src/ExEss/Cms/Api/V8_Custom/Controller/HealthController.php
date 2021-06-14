<?php
namespace ExEss\Cms\Api\V8_Custom\Controller;

use ExEss\Cms\Component\Health\HealthService;
use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Service\User\DefaultUser;
use ExEss\Cms\Command\Traits\LoginTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HealthController extends AbstractApiController
{
    use LoginTrait;

    private HealthService $service;

    private DefaultUser $user;

    private TokenStorageInterface $tokenStorage;

    public function __construct(HealthService $service, DefaultUser $user, TokenStorageInterface $tokenStorage)
    {
        $this->service = $service;
        $this->user = $user;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $this->login($this->tokenStorage, $this->user);

        return $response
            ->withStatus(200)
            ->withHeader('Content-type', 'application/xml')
            ->write($this->service->getResult());
    }
}
