<?php declare(strict_types=1);

namespace ExEss\Cms\Http\Request;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractJsonBody
{
    /**
     * @var array|OptionsResolver[]
     */
    protected static array $resolversByClass = [];
    protected array $arguments = [];

    public function configure(array ...$arguments): self
    {
        // get the class name
        $class = static::class;

        $arguments = \array_merge(...$arguments);

        // store resolver in object for possible re-usage
        if (!isset(self::$resolversByClass[$class])) {
            $optionsResolver = new OptionsResolver();
            self::$resolversByClass[$class] = $optionsResolver;

            $this->configureOptions(self::$resolversByClass[$class]);
        }

        $this->arguments = self::$resolversByClass[$class]->resolve($arguments);

        return $this;
    }

    abstract protected function configureOptions(OptionsResolver $resolver): void;
}
