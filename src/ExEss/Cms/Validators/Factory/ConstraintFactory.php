<?php
namespace ExEss\Cms\Validators\Factory;

use ExEss\Cms\Validators\Exception\ConstraintNotFoundException;
use Symfony\Component\Validator\Constraint;

class ConstraintFactory
{
    private array $map;

    /**
     * @param array $map Map of valid constraints.
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * Creates a constraint object from the constraint name
     *
     * @param null|mixed $params
     * @throws \ExEss\Cms\Validators\Exception\ConstraintNotFoundException When an invalid $name is provided.
     */
    public function createFromName(string $name, $params = null): Constraint
    {
        $name = \ucfirst($name);

        if (!isset($this->map[$name])) {
            throw ConstraintNotFoundException::fromName($name);
        }

        $className = $this->map[$name];

        if (!\property_exists($className, 'model')) {
            unset($params['model']);
        }

        return new $className($params);
    }
}
