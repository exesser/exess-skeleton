<?php declare(strict_types=1);

namespace ExEss\Cms\Api\V8_Custom\Params;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Abstract class to setup framework to convert params and arguments to the necessary items.
 */
abstract class AbstractParams implements \JsonSerializable, ResetInterface
{
    protected array $arguments = [];

    protected static array $resolversByClass = [];

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

    /**
     * Method to configure the options passed through this class.
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException In case of invalid access.
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException In case of invalid option.
     */
    abstract protected function configureOptions(OptionsResolver $resolver): void;

    public function toArray(): array
    {
        return $this->arguments;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __clone()
    {
        $this->reset();
    }

    public function reset(): void
    {
        self::$resolversByClass = [];
    }
}
