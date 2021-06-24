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
}
