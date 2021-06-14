<?php
namespace ExEss\Cms\FLW_Flows\Response;

class ProgressStep implements \JsonSerializable
{
    private bool $active = false;

    private bool $canBeActivated = true;

    private bool $disabled = false;

    private int $progressPercentage = 50;

    private StepValidate $valid;

    private string $id;

    private string $key_c;

    private string $name;

    private string $type_c;

    public function __construct(string $id, string $key, string $name, string $type)
    {
        $this->id = $id;
        $this->key_c = $key;
        $this->name = $name;
        $this->type_c = $type;

        // hardcoded, not used at the moment
        $this->valid = new StepValidate();
        $this->valid->result = true;
    }

    public function isActive(bool $active): ProgressStep
    {
        $this->active = $active;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key_c;
    }

    public function jsonSerialize(): array
    {
        return \get_object_vars($this);
    }
}
