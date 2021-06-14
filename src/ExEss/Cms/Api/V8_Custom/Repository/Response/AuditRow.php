<?php
namespace ExEss\Cms\Api\V8_Custom\Repository\Response;

use ExEss\Cms\Base\Response\BaseResponse;

class AuditRow extends BaseResponse
{
    private \DateTime $timestamp;

    private string $operation;

    private ?string $username = null;

    private ?string $changes = null;

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function getTimestampAsString(): string
    {
        if (!$this->timestamp) {
            return '';
        }

        return $this->timestamp->format('Y-m-d H:i:s');
    }

    public function setTimestamp(string $timestamp): void
    {
        $this->timestamp = new \DateTime($timestamp);
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function setOperation(string $operation): void
    {
        $this->operation = $operation;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username = null): void
    {
        $this->username = $username;
    }

    public function getChanges(): ?string
    {
        return $this->changes;
    }

    public function setChanges(string $changes): void
    {
        $this->changes = $changes;
    }
}
