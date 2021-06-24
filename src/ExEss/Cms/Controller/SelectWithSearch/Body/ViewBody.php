<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\SelectWithSearch\Body;

use ExEss\Cms\FLW_Flows\Response\Model;
use ExEss\Cms\Http\Request\AbstractJsonBody;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ViewBody extends AbstractJsonBody
{
    public function getPage(): int
    {
        return $this->arguments['page'];
    }

    public function getQuery(): ?string
    {
        return $this->arguments['query'];
    }

    public function getKeys(): array
    {
        return $this->arguments['keys'];
    }

    public function getFullModel(): Model
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
            ->setDefault('page', 1)
            ->setAllowedTypes('page', ['int', 'string'])
            ->addNormalizer('page', function (Options $option, $value): int {
                return \is_string($value) ? \intval($value): $value;
            });

        $resolver
            ->setDefault('query', null)
            ->setAllowedTypes('query', ['string', 'null']);

        $resolver
            ->setDefault('keys', [])
            ->setAllowedTypes('keys', ['array']);

        $resolver
            ->setDefault('fullModel', [])
            ->setAllowedTypes('fullModel', ['array', Model::class])
            ->addNormalizer('fullModel', function (Options $option, $value): Model {
                return \is_array($value) ? new Model($value) : $value;
            });

        $resolver
            ->setDefault('params', [])
            ->setAllowedTypes('params', ['array']);
    }
}
