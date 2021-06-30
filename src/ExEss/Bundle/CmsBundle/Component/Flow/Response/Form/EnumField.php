<?php

namespace ExEss\Bundle\CmsBundle\Component\Flow\Response\Form;

use ExEss\Bundle\CmsBundle\Component\Flow\EnumRecord;

class EnumField extends Field
{
    public const TYPE = 'enum';

    /**
     * @var EnumRecord[]
     */
    public array $enumValues = [];

    /**
     * @param EnumRecord[] $enumValues
     */
    public function __construct(string $id, string $label, array $enumValues = [])
    {
        parent::__construct($id, $label, static::TYPE);

        foreach ($enumValues as $value) {
            $this->addValue($value);
        }
    }

    public function addValue(EnumRecord $value): void
    {
        $this->enumValues[] = $value;
    }
}
