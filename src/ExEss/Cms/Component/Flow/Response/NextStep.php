<?php
namespace ExEss\Cms\Component\Flow\Response;

use ExEss\Cms\Entity\FlowStep;

class NextStep implements \JsonSerializable
{
    private ?FlowStep $flowStep = null;

    private ?string $recordId = null;

    private bool $lastStep = false;

    /**
     * @todo check if DWP needs this, not used in backend
     * @var null
     */
    private $actionId;

    /**
     * @todo check if DWP needs this, not used in backend
     * @var null
     */
    private $mainMenuKey;

    public function getFlowStep(): ?FlowStep
    {
        return $this->flowStep;
    }

    public function setFlowStep(FlowStep $flowStep): void
    {
        $this->flowStep = $flowStep;
    }

    public function getRecordId(): ?string
    {
        return $this->recordId;
    }

    public function setRecordId(?string $recordId = null): void
    {
        $this->recordId = $recordId;
    }

    public function isLastStep(): bool
    {
        return $this->lastStep;
    }

    public function setLastStep(bool $lastStep): void
    {
        $this->lastStep = $lastStep;
    }

    public function jsonSerialize(): \stdClass
    {
        return (object) [
            'nextStep' => $this->flowStep ? $this->flowStep->getKey() : null,
            'actionId' => $this->actionId,
            'recordId' => $this->recordId,
            'lastStep' => $this->lastStep,
            'mainMenuKey' => $this->mainMenuKey,
        ];
    }
}
