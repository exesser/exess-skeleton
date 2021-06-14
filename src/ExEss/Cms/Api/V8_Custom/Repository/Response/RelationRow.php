<?php declare(strict_types=1);

namespace ExEss\Cms\Api\V8_Custom\Repository\Response;

class RelationRow implements \JsonSerializable
{
    public string $sourceAssociation;

    public object $source;

    public string $targetAssociation;

    public object $target;

    public function __construct(
        object $source,
        string $sourceAssociation,
        object $target,
        string $targetAssociation
    ) {
        $this->source = $source;
        $this->sourceAssociation = $sourceAssociation;
        $this->target = $target;
        $this->targetAssociation = $targetAssociation;
    }

    public function getSourceAssociation(): string
    {
        return $this->sourceAssociation;
    }

    public function getSource(): object
    {
        return $this->source;
    }

    public function getTargetAssociation(): string
    {
        return $this->targetAssociation;
    }

    public function getTarget(): object
    {
        return $this->target;
    }

    public function getId(): string
    {
        return \join('::', [$this->getSource()->getId(), $this->getTarget()->getId()]);
    }

    public function jsonSerialize(): array
    {
        return [
            $this->getSourceAssociation() . '_id' => $this->getTarget()->getId(),
            $this->getTargetAssociation() . '_id' => $this->getSource()->getId(),
        ];
    }
}
