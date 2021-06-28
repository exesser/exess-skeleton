<?php declare(strict_types=1);

namespace ExEss\Cms;

use ExEss\Cms\DependencyInjection\Compiler\CachePass;
use ExEss\Cms\DependencyInjection\Compiler\GuzzleClientPass;
use ExEss\Cms\DependencyInjection\Compiler\SoapServicesClientPass;
use ExEss\Cms\DependencyInjection\Compiler\SoapServicesPass;
use ExEss\Cms\DependencyInjection\Container;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * We definitely don't want this as a base container here but oh well, our test are just awesomely crazy fucked up
     * We mock services after the container has already initialized that same services, and things like that,
     * some mocks we set don't work actually aren't even used, I've looked at it the last two days
     * and don't even know anymore what I'm doing. Let's keep it for another day or someone else who feels they have
     * the motivation to spend a month fixing everything.
     */
    protected function getContainerBaseClass(): string
    {
        return Container::class;
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CachePass());
        $container->addCompilerPass(new SoapServicesPass());
        $container->addCompilerPass(new SoapServicesClientPass());
        $container->addCompilerPass(new GuzzleClientPass());
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', \PHP_VERSION_ID < 70400 || $this->debug);
        $container->setParameter('container.dumper.inline_factories', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/'.$this->environment.'/*'.self::CONFIG_EXTS, 'glob');
        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, 'glob');
    }
}
