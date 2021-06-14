<?php
namespace ExEss\Cms\Dashboard\Model\Grid\Row\Option;

use ExEss\Cms\Dashboard\Model\StripEmptyOnEncodeTrait;

class Item implements \JsonSerializable
{
    use StripEmptyOnEncodeTrait;

    private ?string $formKey = null;

    private ?string $label = null;

    /**
     * @throws \InvalidArgumentException In case the argument contains unsupported options.
     */
    public function __construct(array $source)
    {
        if (($formKey = $source['formKey'] ?? false) !== false) {
            $this->setFormKey($formKey);
            unset($source['formKey']);
        }
        if (($label = $source['label'] ?? false) !== false) {
            $this->setLabel($label);
            unset($source['label']);
        }

        if (\count($source)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unsupported item options: %s',
                \implode(', ', \array_keys($source))
            ));
        }
    }

    public function getFormKey(): ?string
    {
        return $this->formKey;
    }

    public function setFormKey(string $formKey): Item
    {
        $this->formKey = $formKey;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): Item
    {
        $this->label = $label;

        return $this;
    }
}
