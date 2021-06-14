<?php
namespace ExEss\Cms\Api\V8_Custom\Params;

use Doctrine\ORM\EntityManager;
use ExEss\Cms\Api\V8_Custom\Params\Normalizer\Trim;
use ExEss\Cms\Api\V8_Custom\Params\Validator\ValidatorFactory;
use ExEss\Cms\Entity\FlowAction;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ActionParams extends AbstractParams
{
    private EntityManager $em;

    public function __construct(ValidatorFactory $validatorFactory, EntityManager $em)
    {
        parent::__construct($validatorFactory);
        $this->em = $em;
    }

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
            ->setRequired('action')
            ->setAllowedTypes('action', ['string'])
            ->setAllowedValues('action', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
            ]))
            ->setNormalizer('action', function (Options $options, string $value): FlowAction {
                return $this->em->getRepository(FlowAction::class)->get($value);
            })
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
            ->setAllowedTypes('params', ['array']);
        $resolver
            ->setDefault('recordType', null)
            ->setAllowedTypes('recordType', ['string', 'null'])
            ->setNormalizer('recordType', Trim::asClosure());
        $resolver
            ->setDefault('recordId', null)
            ->setAllowedTypes('recordId', ['string', 'null', 'int'])
            ->setNormalizer('recordId', Trim::asClosure())
            ->setAllowedValues('recordId', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
                new Assert\Regex(['pattern' => self::REGEX_KEY]),
            ], true));
        $resolver
            ->setDefault('recordIds', null)
            ->setAllowedTypes('recordIds', ['array', 'null'])
            ->setAllowedValues('recordIds', $this->validatorFactory->createClosureForIterator([
                new Assert\NotBlank(),
            ], true));
        $resolver
            ->setDefault('actionData', [])
            ->setAllowedTypes('actionData', ['array']);

        $resolver->setDefined('id');
    }
}
