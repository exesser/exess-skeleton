<?php declare(strict_types=1);

namespace Test\Functional\ExEss\Bundle\CmsBundle\Component\ExpressionParser;

class TestObject implements \JsonSerializable
{
    private ?\DateTime $effectiveDate = null;

    private ?\DateTime $creationDateTime = null;

    public function getEffectiveDate(): ?\DateTime
    {
        return $this->effectiveDate;
    }

    public function getCreationDateTime(): ?\DateTime
    {
        return $this->creationDateTime;
    }

    public function jsonSerialize(): array
    {
        return \get_object_vars($this);
    }
}
