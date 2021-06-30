<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\Http\Factory;

use Doctrine\ORM\EntityManagerInterface;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Params\Validator\ValidatorFactory;
use ExEss\Bundle\CmsBundle\AwareTrait\EntityManagerAwareTrait;
use ExEss\Bundle\CmsBundle\AwareTrait\ValidatorFactoryAwareTrait;
use ExEss\Bundle\CmsBundle\Http\Request\AbstractJsonBody;

class JsonBodyFactory
{
    private ValidatorFactory $validatorFactory;
    private EntityManagerInterface $em;

    public function __construct(
        ValidatorFactory $validatorFactory,
        EntityManagerInterface $em
    ) {
        $this->validatorFactory = $validatorFactory;
        $this->em = $em;
    }

    public function create(string $jsonBodyClass): AbstractJsonBody
    {
        $traits = \array_keys((new \ReflectionClass($jsonBodyClass))->getTraits());

        $body = new $jsonBodyClass;
        if (\in_array(EntityManagerAwareTrait::class, $traits, true)) {
            $body->setEntityManager($this->em);
        }
        if (\in_array(ValidatorFactoryAwareTrait::class, $traits, true)) {
            $body->setValidatorFactory($this->validatorFactory);
        }

        return $body;
    }
}
