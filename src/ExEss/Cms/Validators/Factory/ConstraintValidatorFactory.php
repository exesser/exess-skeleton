<?php

namespace ExEss\Cms\Validators\Factory;

use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;

class ConstraintValidatorFactory extends \Symfony\Component\Validator\ConstraintValidatorFactory
{
    private ContainerInterface $container;

    private array $validatorMap;

    public function __construct(ContainerInterface $container, array $validatorMap)
    {
        parent::__construct();

        $this->validatorMap = $validatorMap;
        $this->container = $container;
    }

    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $className = $constraint->validatedBy();

        if (!isset($this->validators[$className]) && \in_array($className, $this->validatorMap, true)) {
            $this->validators[$className] = $this->container->get($className);
        }

        return parent::getInstance($constraint);
    }
}
