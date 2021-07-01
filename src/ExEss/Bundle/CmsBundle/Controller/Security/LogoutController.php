<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\Security;

use Symfony\Component\Routing\Annotation\Route;

class LogoutController
{
    /**
     * @Route("/logout", name="exess_cms_logout", methods={"GET"})
     */
    public function __invoke(): void
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
