<?php
namespace ExEss\Cms\Robo\Task;

use ExEss\Cms\Kernel;
use Psr\Container\ContainerInterface;
use ExEss\Cms\Api\V8_Custom\Service\User\DefaultUser;
use ExEss\Cms\Command\Traits\LoginTrait;
use Symfony\Component\Dotenv\Dotenv;

trait TaskHelper
{
    use LoginTrait;

    private ?ContainerInterface $container = null;

    protected function getContainer(): ContainerInterface
    {
        if (!$this->container) {
            (new Dotenv())->bootEnv(__DIR__ . '/../../../../../.env');

            $kernel = new Kernel($_ENV['APP_ENV'] ?? 'prod', ((bool) $_ENV['APP_DEBUG']) ?? false);
            $kernel->boot();

            if (!\defined('I_AM_INSTALLING')) {
                // all commands are to be performed as the system user
                $this->login(
                    $kernel->getContainer()->get('security.token_storage'),
                    $kernel->getContainer()->get(DefaultUser::class),
                );
            }

            $this->container = $kernel->getContainer();
        }

        return $this->container;
    }
}
