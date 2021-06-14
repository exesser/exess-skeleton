<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Codeception\Traits;

use ExEss\Cms\DependencyInjection\Container;

trait ServiceActions
{
    /**
     * @return mixed
     */
    abstract public function grabService(string $service);

    /**
     * @param mixed $mockedService
     */
    public function mockService(string $name, $mockedService): void
    {
        /** @var Container $container */
        $container = $this->grabService('service_container');
        $container->mockSymfonyService($name, $mockedService);
    }
}
