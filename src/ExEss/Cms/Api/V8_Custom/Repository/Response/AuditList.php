<?php
namespace ExEss\Cms\Api\V8_Custom\Repository\Response;

use ExEss\Cms\Base\Response\BaseListResponse;

class AuditList extends BaseListResponse
{
    /**
     * @var AuditRow[]
     */
    private array $audits = [];

    /**
     * @inheritdoc
     *
     * @return AuditRow[]
     */
    public function getList(): iterable
    {
        return $this->audits;
    }

    /**
     * @param AuditRow[] $audits
     *
     * @throws \DomainException When the array of audits is not an array of audits.
     */
    public function setList(array $audits): void
    {
        foreach ($audits as $audit) {
            if (!$audit instanceof AuditRow) {
                throw new \DomainException('Audited rows not set');
            }
        }

        $this->audits = $audits;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getList();
    }
}
