<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LogParams extends AbstractParams
{
    public function getName(): string
    {
        return $this->arguments['name'];
    }

    public function getState(): array
    {
        return $this->arguments['state'];
    }

    public function getUrl(): string
    {
        return $this->arguments['url'];
    }

    public function getStack(): ?string
    {
        return $this->arguments['stack'];
    }

    public function getCause(): ?string
    {
        return $this->arguments['cause'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('name')
            ->setAllowedTypes('name', ['string'])
            ->setAllowedValues('name', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
            ]));

        $resolver
            ->setRequired('state')
            ->setAllowedTypes('state', ['array']);

        $resolver
            ->setRequired('url')
            ->setAllowedTypes('url', ['string'])
            ->setAllowedValues('url', $this->validatorFactory->createClosure([
                new Assert\Url(),
            ]));

        $resolver
            ->setDefault('stack', null)
            ->setAllowedTypes('stack', ['string', 'null'])
            ->setAllowedValues('stack', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
            ], true));

        $resolver
            ->setDefault('cause', null)
            ->setAllowedTypes('cause', ['string', 'null'])
            ->setAllowedValues('cause', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
            ], true));
    }
}
