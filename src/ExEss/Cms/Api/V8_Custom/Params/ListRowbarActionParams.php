<?php

namespace ExEss\Cms\Api\V8_Custom\Params;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ListRowbarActionParams extends AbstractParams
{
    public function getListKey(): string
    {
        return $this->arguments['listKey'];
    }

    public function getRecordId(): string
    {
        return $this->arguments['recordId'];
    }

    public function getActionData(): array
    {
        return $this->arguments['actionData'];
    }

    /**
     * Method to configure the options passed through this class.
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['listKey', 'recordId']);

        $resolver
            ->setAllowedTypes('listKey', ['string'])
            ->setAllowedValues('listKey', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
                new Assert\Regex(['pattern' => self::REGEX_KEY]),
            ]));

        $resolver
            ->setAllowedTypes('recordId', ['string'])
            ->setAllowedValues('recordId', $this->validatorFactory->createClosure([
                new Assert\NotBlank(),
            ]));

        $resolver
            ->setDefault('actionData', [])
            ->setAllowedTypes('actionData', ['array']);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {

        $options = [
            'listKey' => $this->getListKey(),
            'recordId' => $this->getRecordId(),
        ];

        if (!empty($this->getActionData())) {
            $options['actionData'] = $this->getActionData();
        }
        return $options;
    }
}
