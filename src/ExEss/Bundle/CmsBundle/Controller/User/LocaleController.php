<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Controller\User;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\Security;
use ExEss\Bundle\CmsBundle\Exception\NotAuthenticatedException;
use ExEss\Bundle\CmsBundle\Http\SuccessResponse;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController
{
    private Security $security;
    private EntityManagerInterface $manager;

    public function __construct(Security $security, EntityManagerInterface $manager)
    {
        $this->security = $security;
        $this->manager = $manager;
    }

    /**
     * @Route("/Api/user/change-locale/{locale}", methods={"POST"})
     */
    public function __invoke(string $locale): SuccessResponse
    {
        if (null === $user = $this->security->getCurrentUser()) {
            throw new NotAuthenticatedException('Token not found');
        }

        $user->setPreferredLocale($locale);

        $this->manager->persist($user);
        $this->manager->flush();

        return new SuccessResponse();
    }
}
