<?php declare(strict_types=1);

namespace ExEss\Cms\Controller\Action\Body;

use ExEss\Cms\Api\V8_Custom\Params\Normalizer\Trim;
use ExEss\Cms\AwareTrait\ValidatorFactoryAwareTrait;
use ExEss\Cms\Entity\FlowAction;
use ExEss\Cms\Http\Request\AbstractJsonBody;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ExecuteBody extends AbstractJsonBody
{
    use ValidatorFactoryAwareTrait;
    public function getAction(): FlowAction
    {
        return $this->arguments['action'];
    }

    public function getListKey(): ?string
    {
        return $this->arguments['listKey'];
    }

    public function getParams(): array
    {
        return $this->arguments['params'];
    }

    public function getRecordType(): ?string
    {
        return $this->arguments['recordType'];
    }

    public function getRecordId(): ?string
    {
        return $this->arguments['recordId'];
    }

    public function getRecordIds(): ?array
    {
        return $this->arguments['recordIds'];
    }

    public function getActionData(): array
    {
        return $this->arguments['actionData'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined('id')
        ;
        $resolver
            ->setDefault('listKey', null)
            ->setAllowedTypes('listKey', ['string', 'null'])
            ->setNormalizer('listKey', Trim::asClosure())
            ->setAllowedValues('listKey', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
            ], true));
        $resolver
            ->setDefault('params', [])
            ->setAllowedTypes('params', ['array'])
        ;
        $resolver
            ->setDefault('recordType', null)
            ->setAllowedTypes('recordType', ['string', 'null'])
            ->setNormalizer('recordType', function (Options $options, ?string $value): ?string {
                return (new Trim())->normalize($options['params']['recordType'] ?? $value);
            })
        ;
        $resolver
            ->setDefault('recordId', null)
            ->setAllowedTypes('recordId', ['string', 'null', 'int'])
            ->setNormalizer('recordId', Trim::asClosure())
            ->setAllowedValues('recordId', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
            ], true))
        ;
        $resolver
            ->setDefault('recordIds', null)
            ->setAllowedTypes('recordIds', ['array', 'null'])
            ->setAllowedValues('recordIds', $this->validatorFactory->createClosureForIterator([
                new Assert\NotBlank(),
            ], true))
        ;
        $resolver
            ->setDefault('actionData', [])
            ->setAllowedTypes('actionData', ['array'])
        ;
    }
}
