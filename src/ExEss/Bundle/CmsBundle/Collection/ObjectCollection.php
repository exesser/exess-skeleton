<?php
namespace ExEss\Bundle\CmsBundle\Collection;

class ObjectCollection extends \ArrayIterator implements \JsonSerializable
{
    private string $className;

    /**
     * @throws \InvalidArgumentException When the objectClass doesn't exist.
     */
    public function __construct(string $className, array $elements = [], int $flags = 0)
    {
        $className = $this->transformToClassName($className);

        if (!\class_exists($className) && !\interface_exists($className)) {
            throw new \InvalidArgumentException(\sprintf('Class or interface %s doesn\'t exist', $className));
        }
        $this->className = $className;

        parent::__construct($elements, $flags);
    }

    /**
     * @param mixed $index
     * @param mixed $value
     *
     * @throws \InvalidArgumentException If the value is not an object or has an incorrect type.
     */
    public function offsetSet($index, $value): void
    {
        $index = $this->transformToClassName($index);

        if (!$value instanceof $this->className) {
            throw new \InvalidArgumentException(\sprintf(
                'Incorrect value argument, must be instance of %s',
                $this->className
            ));
        }

        parent::offsetSet($index, $value);
    }

    /**
     * @param mixed $index
     * @return null|object of class $this->className
     */
    public function offsetGet($index): ?object
    {
        $index = $this->transformToClassName($index);

        return parent::offsetGet($index);
    }

    /**
     * @param mixed $index
     */
    public function offsetExists($index): bool
    {
        $index = $this->transformToClassName($index);

        return parent::offsetExists($index);
    }

    /**
     * TEMP HACK: this can be removed if we don't use module name anymore for the index
     *
     * @param mixed|null $index
     * @return mixed
     *
     * @deprecated please use the class name instead of a module name
     */
    protected function transformToClassName($index = null)
    {
        // temp hack
        switch ((string) $index) {
            case 'SecurityGroups':
                $index = \SecurityGroup::class;
                break;
            case 'Users':
                $index = User::class;
                break;
        }

        return $index;
    }

    public function filter(\Closure $callable): array
    {
        return \array_filter(
            $this->getArrayCopy(),
            $callable
        );
    }

    public function column(string $column): array
    {
        return \array_column(
            $this->getArrayCopy(),
            $column
        );
    }

    public function jsonSerialize(): array
    {
        return $this->getArrayCopy();
    }

    public function isClass(string $className): bool
    {
        return $this->className === $this->transformToClassName($className);
    }
}
