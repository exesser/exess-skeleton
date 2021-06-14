<?php declare(strict_types=1);

namespace ExEss\Cms\Adapter\Request;

use ExEss\Cms\Api\V8_Custom\Params\Validator\ValidatorFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractOptionRequest extends AbstractRequest
{
    protected ValidatorFactory $validatorFactory;

    public function __construct(ValidatorFactory $validatorFactory, ?array $parameters = null, array $headers = [])
    {
        $this->validatorFactory = $validatorFactory;

        parent::__construct($parameters, $headers);
    }

    public function getParameters(): array
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);

        return $optionsResolver->resolve($this->parameters);
    }

    /**
     * Method to configure the options passed through this class.
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException In case of invalid access.
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException In case of invalid option.
     */
    abstract protected function configureOptions(OptionsResolver $resolver): void;
}
