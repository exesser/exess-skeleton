<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

use ExEss\Cms\Api\V8_Custom\Params\Validator\ValidatorFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Abstract class to setup framework to convert params and arguments to the necessary items.
 */
abstract class AbstractParams implements \JsonSerializable, ResetInterface
{
    public const REGEX_KEY = '/[A-Za-z0-9-_]*/';

    protected array $arguments = [];

    protected ValidatorFactory $validatorFactory;

    protected static array $resolversByClass = [];

    public function __construct(ValidatorFactory $validatorFactory)
    {
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * @return $this
     */
    public function configure(array ...$arguments)
    {
        // get the class name
        $class = static::class;

        $arguments = \array_merge(...$arguments);

        // store resolver in object for possible re-usage
        if (!\array_key_exists($class, self::$resolversByClass)) {
            $optionsResolver = new OptionsResolver();
            self::$resolversByClass[$class] = $optionsResolver;

            $this->configureOptions(self::$resolversByClass[$class]);
        }

        $this->setDefined(self::$resolversByClass[$class], $arguments);
        $this->arguments = self::$resolversByClass[$class]->resolve($arguments);

        return $this;
    }

    public function setDefined(OptionsResolver $optionsResolver, array $arguments): void
    {
        // do nothing
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
