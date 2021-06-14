<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectWithSearchParams extends AbstractParams
{
    public function getSelectWithSearchName(): string
    {
        return $this->arguments['selectWithSearchName'];
    }

    public function getPage(): ?int
    {
        return $this->arguments['page'];
    }

    public function getQuery(): string
    {
        return $this->arguments['query'];
    }

    public function getKeys(): array
    {
        return $this->arguments['keys'];
    }

    public function getFullModel(): array
    {
        return $this->arguments['fullModel'];
    }

    public function getParams(): array
    {
        return $this->arguments['params'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('selectWithSearchName')
            ->setAllowedTypes('selectWithSearchName', ['string']);

        $resolver
            ->setDefault('page', 1)
            ->setAllowedTypes('page', ['int', 'string', 'null'])
            ->addNormalizer('page', function (Options $option, $value): ?int {
                return $value ? \intval($value): null;
            });

        $resolver
            ->setDefault('query', '')
            ->setAllowedTypes('query', ['string']);

        $resolver
            ->setDefault('keys', [])
            ->setAllowedTypes('keys', ['array']);

        $resolver
            ->setDefault('fullModel', [])
            ->setAllowedTypes('keys', ['array']);

        $resolver
            ->setDefault('params', [])
            ->setAllowedTypes('keys', ['array']);
    }
}
