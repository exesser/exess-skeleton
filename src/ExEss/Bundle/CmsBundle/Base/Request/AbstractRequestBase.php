<?php

namespace ExEss\Bundle\CmsBundle\Base\Request;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractRequestBase implements \JsonSerializable
{
    protected array $options = [];

    /**
     * Defines the requirements for the options
     *
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException When options are not accessible.
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException When undefined options are passed.
     */
    abstract protected function configureOptions(OptionsResolver $resolver): void;

    /**
     * @return array
     */
    abstract protected function jsonSerializeTemplate(): array;

    /**
     * requires the options to be resolved to be passed here
     * stores the option configuration and resolves the passed options
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException When options are not accessible.
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException When undefined options are passed.
     */
    public function resolve(array $options): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined(\array_keys($options));
        $this->configureExtraOptions($optionsResolver);
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);
    }

    /**
     * Defines the requirements for the options
     *
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException When options are not accessible.
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException When undefined options are passed.
     */
    protected function configureExtraOptions(OptionsResolver $resolver): void
    {
        return;
    }

    /**
     * @return array
     */
    protected function jsonSerializeExtra(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return \array_merge($this->jsonSerializeTemplate(), $this->jsonSerializeExtra());
    }
}
