<?php
namespace ExEss\Bundle\CmsBundle\Component\Flow\Response;

use ExEss\Bundle\CmsBundle\Entity\FlowStep;
use stdClass;

class CurrentStep implements \JsonSerializable
{
    private bool $willSave;

    /**
     * @var null
     */
    private $done;

    private NextStep $nextStep;

    private FlowStep $flowStep;

    public function __construct(bool $willSave, NextStep $nextStep, FlowStep $flowStep)
    {
        $this->willSave = $willSave;
        $this->nextStep = $nextStep;
        $this->flowStep = $flowStep;
    }

    public function getFlowStep(): FlowStep
    {
        return $this->flowStep;
    }

    public function jsonSerialize(): stdClass
    {
        return (object) [
            'willSave' => $this->willSave,
            'done' => $this->done,
            'next' => $this->nextStep,
        ];
    }
}
