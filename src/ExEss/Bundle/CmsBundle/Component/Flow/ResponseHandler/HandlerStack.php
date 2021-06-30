<?php

namespace ExEss\Bundle\CmsBundle\Component\Flow\ResponseHandler;

class HandlerStack
{
    /**
     * @var array|Handler[]
     */
    private array $stack = [];

    /**
     * @param Handler[] $handlers
     */
    public function __construct(iterable $handlers = [])
    {
        foreach ($handlers as $handler) {
            $this->push($handler);
        }
    }

    /**
     * @throws \LogicException When the same handler is added twice.
     */
    public function push(Handler $handler): void
    {
        if (isset($this->stack[\get_class($handler)])) {
            throw new \LogicException(\sprintf('You cannot add the same handler twice (%s)', \get_class($handler)));
        }

        $this->stack[\get_class($handler)] = $handler;
    }

    public function has(string $class): bool
    {
        return isset($this->stack[$class]);
    }

    /**
     * @throws \InvalidArgumentException When handler is not found.
     */
    public function get(string $class): Handler
    {
        if (!$this->has($class)) {
            throw new \InvalidArgumentException(\sprintf('ResponseHandler %s was not found', $class));
        }

        return $this->stack[$class];
    }

    /**
     * @return array|Handler[]
     */
    public function toArray(): array
    {
        return $this->stack;
    }
}
