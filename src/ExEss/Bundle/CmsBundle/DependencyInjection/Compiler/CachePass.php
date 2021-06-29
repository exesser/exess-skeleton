<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\DependencyInjection\Compiler;

use ExEss\Cms\Cache\Cache;
use ExEss\Cms\Cache\CacheAdapterFactory;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class CachePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(CacheAdapterFactory::class)) {
            return;
        }

        foreach (Cache::CACHE_POOLS as $pool => $ttl) {
            $definition = new Definition(AdapterInterface::class);
            $definition->setPublic(true);
            $definition->setFactory([new Reference(CacheAdapterFactory::class), 'create']);
            $definition->setArguments([
                $pool,
                Cache::getTtl($ttl)
            ]);

            $container->setDefinition($pool, $definition);
            $container->setAlias(
                'Symfony\Component\Cache\Adapter\AdapterInterface $' . $this->translateConstantToVar(
                    \str_replace(
                        'caches.',
                        '',
                        $pool
                    ),
                    'Cache'
                ),
                $pool
            );
        }
    }

    private function translateConstantToVar(string $constant, string $suffix): string
    {
        return \preg_replace_callback('/[._-][a-z]?/', function (array $matches): string {
                return \strtoupper(\ltrim($matches[0], '._-'));
            }, \strtolower($constant)) . $suffix;
    }
}
