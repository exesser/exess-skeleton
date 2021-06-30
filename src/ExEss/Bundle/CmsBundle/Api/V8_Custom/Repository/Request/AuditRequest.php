<?php
namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\Request;

use ExEss\Bundle\CmsBundle\Base\Request\AbstractRequest;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuditRequest extends AbstractRequest
{
    public function getRecordId(): string
    {
        return $this->options['recordId'];
    }

    public function getRecordType(): string
    {
        return $this->options['recordType'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('recordId', null)
            ->setAllowedTypes('recordId', ['null', 'string']);

        $resolver
            ->setRequired('recordType')
            ->setAllowedTypes('recordType', ['string']);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerializeTemplate(): array
    {
        return [];
    }
}
