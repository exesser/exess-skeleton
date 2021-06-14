<?php
namespace ExEss\Cms\FLW_Flows\AfterSave;

use ExEss\Cms\Collection\ObjectCollection;
use ExEss\Cms\FLW_Flows\Handler\FlowData;

class AfterSaveData
{
    private FlowData $flowData;

    private ObjectCollection $subFlowData;

    public function __construct(FlowData $flowData)
    {
        $this->flowData = $flowData;
        $this->subFlowData = new ObjectCollection(FlowData::class);
    }

    public function getFlowData(): FlowData
    {
        return $this->flowData;
    }

    /**
     * @return ObjectCollection|FlowData[]
     */
    public function getSubFlowData(): ObjectCollection
    {
        return $this->subFlowData;
    }

    public function addSubFlowData(FlowData $flowData): void
    {
        $this->subFlowData[] = $flowData;
    }
}
