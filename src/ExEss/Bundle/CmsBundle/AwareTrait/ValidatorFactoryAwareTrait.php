<?php declare(strict_types=1);

namespace ExEss\Bundle\CmsBundle\AwareTrait;

use ExEss\Bundle\CmsBundle\Api\V8_Custom\Params\Validator\ValidatorFactory;

trait ValidatorFactoryAwareTrait
{
    protected ValidatorFactory $validatorFactory;

    public function setValidatorFactory(ValidatorFactory $validatorFactory): void
    {
        $this->validatorFactory = $validatorFactory;
    }
}
