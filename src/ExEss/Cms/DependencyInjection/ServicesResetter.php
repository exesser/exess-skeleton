<?php declare(strict_types=1);

namespace ExEss\Cms\DependencyInjection;

use Symfony\Contracts\Service\ResetInterface;

class ServicesResetter implements ResetInterface
{
    private \Traversable $resettableServices;

    public function __construct(\Traversable $resettableServices)
    {
        $this->resettableServices = $resettableServices;
    }

    public function reset(): void
    {
        foreach ($this->resettableServices as $service) {
            if ($service instanceof ResetInterface) {
                $service->reset();
            }
        }
    }
}
