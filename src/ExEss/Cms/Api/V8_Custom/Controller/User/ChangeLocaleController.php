<?php

namespace ExEss\Cms\Api\V8_Custom\Controller\User;

use Doctrine\ORM\EntityManagerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use ExEss\Cms\Api\Core\AbstractApiController;
use ExEss\Cms\Api\V8_Custom\Params\ChangeLocaleParams;
use ExEss\Cms\Api\V8_Custom\Service\Security;

class ChangeLocaleController extends AbstractApiController
{
    private Security $security;

    private EntityManagerInterface $manager;

    public function __construct(Security $security, EntityManagerInterface $manager)
    {
        $this->security = $security;
        $this->manager = $manager;
    }

    public function __invoke(Request $req, Response $res, array $args, ChangeLocaleParams $params): Response
    {
        $currentUser = $this->security->getCurrentUser();
        $currentUser->setPreferredLocale($params->getLocale());

        $this->manager->persist($currentUser);
        $this->manager->flush();

        return $this->generateResponse($res, 200);
    }
}
