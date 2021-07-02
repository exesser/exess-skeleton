<?php
namespace App\Robo\Task;

use App\Kernel;
use Psr\Container\ContainerInterface;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Service\User\DefaultUser;
use ExEss\Bundle\CmsBundle\Command\Traits\LoginTrait;
use Symfony\Component\Dotenv\Dotenv;

trait TaskHelper
{
    use LoginTrait;

    private ?ContainerInterface $container = null;

    protected function getContainer(): ContainerInterface
    {
        if (!$this->container) {
            (new Dotenv())->bootEnv(__DIR__ . '/../../.env');

            $kernel = new Kernel($_ENV['APP_ENV'] ?? 'prod', ((bool) $_ENV['APP_DEBUG']) ?? false);
            $kernel->boot();

            // all commands are to be performed as the system user
            $this->login(
                $kernel->getContainer()->get('security.token_storage'),
                $kernel->getContainer()->get(DefaultUser::class),
            );

            $this->container = $kernel->getContainer();
        }

        return $this->container;
    }
}
