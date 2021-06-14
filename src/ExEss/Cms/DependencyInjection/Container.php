<?php declare(strict_types=1);

namespace ExEss\Cms\DependencyInjection;

use Symfony\Component\DependencyInjection\Container as BaseContainer;

class Container extends BaseContainer
{
    /**
     * @param mixed $service
     */
    public function mockSymfonyService(string $id, $service): void
    {
        if ($this->has($id)) {
            if (isset($this->services[$id])) {
                unset($this->services[$id]);
            }
            $this->set($id, $service);
        }
    }

    /**
     * @inheritDoc
     */
    public function get($id, $invalidBehavior = /* self::EXCEPTION_ON_INVALID_REFERENCE */ 1)
    {
        if ($id === 'router') {
            $backtrace = \debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
            if (\substr($backtrace[0]['file'], -\strlen('Slim/App.php')) === 'Slim/App.php') {
                return parent::get('Slim\Router', $invalidBehavior);
            }
        }

        return parent::get($id, $invalidBehavior);
    }
}
