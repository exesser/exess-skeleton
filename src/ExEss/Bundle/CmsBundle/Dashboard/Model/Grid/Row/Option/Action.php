<?php
namespace ExEss\Bundle\CmsBundle\Dashboard\Model\Grid\Row\Option;

use ExEss\Bundle\CmsBundle\Dashboard\Model\StripEmptyOnEncodeTrait;

class Action implements \JsonSerializable
{
    use StripEmptyOnEncodeTrait;

    private ?string $id = null;

    private ?string $recordId = null;

    /**
     * @throws \InvalidArgumentException In case the argument contains unsupported options.
     */
    public function __construct(array $source)
    {
        if (($id = $source['id'] ?? false) !== false) {
            $this->setId($id);
            unset($source['id']);
        }
        if (($recordId = $source['recordId'] ?? false) !== false) {
            $this->setRecordId($recordId);
            unset($source['recordId']);
        }

        if (\count($source)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unsupported action options: %s',
                \implode(', ', \array_keys($source))
            ));
        }
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): Action
    {
        $this->id = $id;

        return $this;
    }

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function setRecordId(string $recordId): Action
    {
        $this->recordId = $recordId;

        return $this;
    }
}
