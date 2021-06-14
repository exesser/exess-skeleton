<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

use ExEss\Cms\Api\V8_Custom\Params\Normalizer\Trim;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class GetDashboardParams extends AbstractParams
{
    public function getDashBoardName(): string
    {
        return $this->arguments['dash_name'];
    }

    public function getRecordId(): ?string
    {
        return $this->arguments['recordId'];
    }

    public function getRecordType(): ?string
    {
        return $this->arguments['recordType'];
    }

    public function getQuery(): string
    {
        return $this->arguments['query'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('dash_name')
            ->setAllowedTypes('dash_name', ['string'])
            ->setAllowedValues('dash_name', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
                new Assert\Regex(['pattern' => self::REGEX_KEY]),
            ], true));

        $resolver
            ->setDefault('recordId', null)
            ->setAllowedTypes('recordId', ['string', 'null', 'int'])
            ->setNormalizer('recordId', Trim::asClosure())
            ->setAllowedValues('recordId', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
            ], true));

        $resolver
            ->setDefault('recordType', null)
            ->setAllowedTypes('recordType', ['string', 'null'])
            ->setAllowedValues('recordType', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
            ], true));

        $resolver
            ->setDefault('query', '')
            ->setAllowedTypes('query', ['string']);
    }
}
